<?php

namespace Lara\Jarvis\Utils;

use Carbon\Carbon;
use geekcom\ValidatorDocs\Rules\Cnpj;
use geekcom\ValidatorDocs\Rules\Cpf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Lara\Jarvis\Enums\Enums;
use Lara\Jarvis\Http\Resources\DefaultResource;

abstract class Helpers
{
    public static function formatDateInterval($start, $end)
    {
        $startDate = new Carbon($start);
        $endDate   = new Carbon($end);

        $emptyDate   = empty($start) || empty($end);
        $emptyHour   = empty($startDate->hour) || empty($endDate->hour);
        $emptySecond = empty($startDate->second) || empty($endDate->second);

        if ($emptyHour || $emptyDate) {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        if ($emptySecond) {
            $startDate->startOfMinute();
            $endDate->endOfMinute();
        }

        return [
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString()
        ];
    }

    public static function addQueryFilters(array $filters, $query)
    {
        $canQueryRelations = $query instanceof Model || $query instanceof Relation;

        $filters  = collect($filters);
        $wheres   = $filters->get('where', []);
        $betweens = $filters->get('between', []);
        $likes    = $filters->get('like', null);
        $searchs  = $filters->get('search', null);

        $order = $filters->get('order', 'id,DESC');
        $order = explode(',', $order);

        $orderField     = $order[0] ?? 'id';
        $orderDirection = $order[1] ?? 'DESC';

        $query = $query->orderBy($orderField, $orderDirection)
            ->where(function ($query) use ($searchs, $canQueryRelations) {
                if (is_array($searchs)) {
                    foreach ($searchs as $item) {
                        $item = explode(',', $item);
                        if (count($item) != 2) {
                            throw new \Exception('Invalid "search" parameters, expected 2 passes ' . count($item));
                        }
                        $item[1] = '%' . $item[1] . '%';

                        if (str_contains($item[0], '.') && $canQueryRelations) {
                            $relation = explode('.', $item[0]);

                            $query->whereHas($relation[0], function ($query) use ($relation, $item) {
                                $query->orWhere($relation[1], 'like', $item[1]);
                            });
                        } else {
                            $query->orWhere($item[0], 'like', $item[1]);
                        }
                    }
                }
            })
            ->where(function ($query) use ($wheres, $likes, $betweens, $canQueryRelations) {
                if (is_array($likes)) {
                    foreach ($likes as $like) {
                        $like = explode(',', $like);
                        if (count($like) != 2) {
                            throw new \Exception('Invalid "like" parameters, expected 2 passes ' . count($like));
                        }

                        $like[1] = '%' . $like[1] . '%';

                        if (str_contains($like[0], '.') && $canQueryRelations) {
                            $relation = explode('.', $like[0]);
                            $query->whereHas($relation[0], function ($query) use ($relation, $like) {
                                $query->where($relation[1], 'like', $like[1]);
                            });
                        } else {
                            $query->where($like[0], 'like', $like[1]);
                        }
                    }
                }

                if (is_array($wheres)) {
                    foreach ($wheres as $where) {
                        $where = explode(',', $where);
                        if (count($where) < 2) {
                            throw new \Exception('Invalid "where" parameters, expected 3 passes ' . count($where));
                        }

                        if ($where[1] == 'in') {
                            if (str_contains($where[0], '.') && $canQueryRelations) {
                                $relations = explode('.', $where[0]);

                                $query->whereHas($relations[0], function ($query) use ($relations, $where) {
                                    $values = array_slice($where, 2);

                                    if (count($relations) > 2) {
                                        $query->whereHas($relations[1], function ($query) use ($relations, $values) {
                                            $query->whereIn($relations[2], $values);
                                        });
                                    } else {
                                        $query->whereIn($relations[1], $values);
                                    }
                                });
                            } else {
                                $query->whereIn($where[0], array_slice($where, 2));
                            }
                        } elseif (str_contains($where[0], '.') && $canQueryRelations) {
                            $relations = explode('.', $where[0]);

                            $query->whereHas($relations[0], function ($query) use ($relations, $where) {
                                $values = array_slice($where, 1);

                                if (count($relations) > 2) {
                                    $query->whereHas($relations[1], function ($query) use ($relations, $values) {
                                        foreach ($values as $value) {
                                            $query->where($relations[2], $value);
                                        }
                                    });
                                } else {
                                    foreach ($values as $value) {
                                        $query->where($relations[1], $value);
                                    }
                                }
                            });
                        } elseif (count($where) === 3) {
                            $query->where($where[0], $where[1], $where[2]);
                        } else {
                            $query->where($where[0], $where[1]);
                        }
                    }
                }

                if (is_array($betweens)) {
                    foreach ($betweens as $between) {
                        $between = explode(',', $between);
                        if (count($between) != 3) {
                            throw new \Exception('Invalid "between" parameters, expected 3 passes ' . count($between));
                        }

                        if (str_contains($between[0], '.') && $canQueryRelations) {
                            $relations = explode('.', $between[0]);

                            $query->whereHas($relations[0], function ($query) use ($relations, $between) {
                                $dates = array_slice($between, 1);

                                if (count($relations) > 2) {
                                    $query->whereHas($relations[1], function ($query) use ($relations, $dates) {
                                        $query->whereBetween(
                                            $relations[2],
                                            self::formatDateInterval($dates[0], $dates[1])
                                        );
                                    });
                                } else {
                                    $query->whereBetween($relations[1], self::formatDateInterval($dates[0], $dates[1]));
                                }
                            });
                        } else {
                            $query->whereBetween($between[0], self::formatDateInterval($between[1], $between[2]));
                        }
                    }
                }
            });

        return $query;
    }

    public static function indexQueryBuilder(
        Request $request,
        array $relationships,
        $model,
        $order_by = 'asc',
        $fields = ['*']
    ) {
        if ($request->paginate === "false") {
            $request->merge(['paginate' => false]);
        }

        if (!$request->order) {
            $request->merge(['order' => "id,$order_by"]);
        }

        $limit = $request->input('limit', 20);

        $query = self::addQueryFilters($request->all(), $model);

        if (!empty($relationships)) {
            $query->with($relationships);
        }

        if ($request->input('paginate', true)) {
            return $query->paginate($limit, $fields);
        } else {
            return $query->get($fields);
        }
    }

    public static function paginateCollection($collection, $perPage = 20, $pageName = 'page', $fragment = null)
    {
        $currentPage      = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage);
        parse_str(request()->getQueryString(), $query);
        unset($query[$pageName]);
        return DefaultResource::collection(
            new \Illuminate\Pagination\LengthAwarePaginator(
                $currentPageItems,
                $collection->count(),
                $perPage,
                $currentPage,
                [
                    'pageName' => $pageName,
                    'path'     => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                    'query'    => $query,
                    'fragment' => $fragment
                ]
            )
        );
    }

    public static function legalEntity($document)
    {
        $cpf = new Cpf();
        if ($cpf->validateCpf(null, $document)) {
            return 'PF';
        }

        $cpf = new Cnpj();
        if ($cpf->validateCnpj(null, $document)) {
            return 'PJ';
        }
    }

    public static function centsToMoney($cents, $pattern = 'BRL')
    {
        return number_format(($cents / 100), 2, $pattern == 'BRL' ? ',' : '.', '');
    }

    public static function userPasswordGenerator($length = 6)
    {
        return strtolower(substr(md5(uniqid()), 0, $length));
    }

    public static function sanitizeString($str)
    {
        $formated_str = preg_replace('/[^0-9]/', '', $str);

        return $formated_str;
    }

    public static function sanitizeStringWithLetters($str)
    {
        $formated_str = preg_replace("~[^A-Za-z0-9]~", '', $str);

        return $formated_str;
    }

    public static function maskDocument($document)
    {
        if (strlen($document) === 11) {
            return self::mask(Enums::MASKS['cpf'], $document);
        } elseif (strlen($document) === 14) {
            return self::mask(Enums::MASKS['cnpj'], $document);
        }
    }

    public static function mask($mask, $str)
    {
        $str = str_replace(" ", "", $str);

        for ($i = 0; $i < strlen($str); $i++) {
            $mask[strpos($mask, "#")] = $str[$i];
        }

        return $mask;
    }

    public static function numberToText($value, $locale = 'pt-br')
    {
        $f = new \NumberFormatter($locale, \NumberFormatter::SPELLOUT);

        return $f->format($value);
    }

    public static function centsToText($value)
    {
        $valString = strval($value);

        $reais = substr($valString, 0, strlen($valString) - 2);

        $cents = substr($valString, -2);

        $text = self::numberToText((int)$reais);

        if ((int)$reais === 1) {
            $text .= ' real';
        } else {
            $text .= ' reais';
        }

        if ((int)$cents > 0) {
            $text .= ' e ' . self::numberToText((int)$cents);

            if ((int)$cents === 1) {
                $text .= ' centavo';
            } else {
                $text .= ' centavos';
            }
        }

        return ucfirst($text);
    }

    public static function genRandomString($length = 10, $steps = 3)
    {
        $characters = '';
        $numbers    = '0123456789';
        $lowercase  = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($steps == 1) {
            $characters .= $numbers;
        } elseif ($steps == 2) {
            $characters .= $numbers . $lowercase;
        } elseif ($steps == 3) {
            $characters .= $numbers . $lowercase . $uppercase;
        }

        $charactersLength = strlen($characters);
        $randomString     = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
