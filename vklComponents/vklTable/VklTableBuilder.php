<?php

namespace VklComponents\VklTable;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator;

/*
 * Example of usage:
 *
        $table = new VklTableBuilder($query, $requestData);

        $table->addCustomColumn('full_name', 'CONCAT(first_name, " ", last_name)')
            ->setCustomFilterForColumn('status', function ($query, $filterValues) use ($requestData) {
                foreach ($filterValues as $filterValue) {
                    switch ($filterValue) {
                        case EventParticipant::STATUS_INCOMPLETE:
                            $query->orWhere('status', EventParticipant::STATUS_INCOMPLETE);
                            break;
                        case EventParticipant::STATUS_CANCELLED:
                            $query->orWhere('status', EventParticipant::STATUS_CANCELLED);
                    }
                }
            })
            ->addCustomOrderColumn('datetime', function ($query, $direction) {
                return $query->orderBy('events.start', $direction)
                    ->orderBy('events_participants.created_at', 'desc');
            })
            ->addCustomExportColumn('start', function ($record) {
                 return VklTableHelper::convertBaseDateTimeToAppDateTime($record['start']);
            })
            ->setSearchableColumns([
                'full_name',
                'email'
            ]);

        return $table->resolve();
 *
 * Example of request:
 *
        search: Ben
        order[]: status:asc
        filter[]: status:incomplete,pending_payment
        limit: 25
        page: 1
        visible_columns[]: full_name
        visible_columns[]: status
 *
 * Example of response:
 *
        [
            "data" => [
                0 => [
                    "id" => 7
                    "first_name" => "Benjamin"
                    "last_name" => "White"
                    "status" => "incomplete"
                ]
                1 => array:20 [
                    "id" => 8
                    "first_name" => "O'leg"
                    "last_name" => "Toker"
                    "status" => "pending_payment"
                ]
            ]
            "amount" => 2
            "perPage" => "25"
            "currentPage" => 1
            "lastPage" => 1
        ]
 *
 * Available methods:
 *
 * ->resolve($applyRequest = true) - Need to execute after all conditions. Apply the query and get the export data.
 * ->addCustomColumn(string $columnName, string $query) - Add query for the custom column.
 * ->setSearchableColumns(array $searchableColumns) - Set array of column names where we should search. By default all columns are searchable.
 * ->setCustomOrderForColumn(string $columnName, Closure $closure) - Set custom order closure for column, to add additional sorting logic. Closure should return query.
 * ->setCustomFilterForColumn(string $columnName, Closure $closure) - Set custom filter closure for column, to add additional filter logic.
 * ->setCustomExportForColumn(string $columnName, Closure $closure) - Set custom format of column data for server table export. Closure should return string.
 * ->addCustomExportType(string $exportTypeName, Closure $closure) - Add custom export type with realization in closure.
 * ->getRequestedColumnFormatsInArray() - Get requested column formats in array (column => format).
 * ->getRequestedVisibleColumnHeaders() - Get array of column names with headers that should be in export (column => header).
 *
 * Helper functions in vklComponents/vklTable/VklTableHelper.php
*/

class VklTableBuilder
{
    // Default maximum number of records per page.
    const DEFAULT_LIMIT = 25;

    /**
     * The columns where we search.
     * @var array
     */
    private $searchableColumns;

    /**
     * Columns with custom filter in closure.
     * @var array
     */
    private $customFilterColumns = [];

    /**
     * Columns with custom order in query.
     * @var array
     */
    private $customOrderColumns = [];

    /**
     * Mapping of columns that can be received in the request but should be mapped to other selection when query is executed.
     * Example: ['full_name' => 'CONCAT(first_name, " ", last_name)']
     * @var array
     */
    private $customColumns = [];

    /**
     * The query builder instance.
     * @var EloquentBuilder|QueryBuilder
     */
    private $query;

    /**
     * The class of output data resource.
     * @var string
     */
    private $resourceClass;

    /**
     * String with requested search value.
     * @var null|string
     */
    private $requestedSearch;

    /**
     * Array of stings with requested columns orders with directions => ['column:direction'].
     * @var array
     */
    private $requestedOrders;

    /**
     * Array of requested column names that should be returned.
     * @var array
     */
    private $requestedColumns;

    /**
     * Array of stings with requested columns filters => ['column:value1,value2'].
     * @var array
     */
    private $requestedFilters;

    /**
     * String with requested export type.
     * Available:
     *  'excel' - to make server export excel file.
     * If the export type is not defined, resolve method will return grid data.
     * @var null|string
     */
    private $requestedExportType;

    /**
     * Array of stings with requested export column titles => ['full_name:Name'].
     * @var array
     */
    private $requestedExportColumnTitles;

    /**
     * Array of stings with requested export column formats => ['shift_date:datetime'].
     * @var array
     */
    private $requestedExportColumnFormats;

    /**
     * Requested maximum number of records per page.
     * 0 - if pagination is not needed
     * @var int
     */
    private $requestedLimit;

    /**
     * Requested page number.
     * @var int
     */
    private $requestedPage;

    /**
     * Custom export types with realization in closure.
     * @var array
     */
    private $customExportTypes = [];

    /**
     * Paginator data.
     * @var array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private $paginate = [];

    /**
     * The result of request.
     *
     * 'data' - Requested records.
     * 'amount' - Amount of filtered records.
     * 'perPage' - The maximum number of records per page.
     * 'currentPage' - The current page number.
     * 'lastPage' - The last page number.
     *
     * @var array
     */
    private $result = [];

    /**
     * Array of requested column names that should be in export.
     * @var array
     */
    public $requestedVisibleColumns;

    /**
     * Columns with custom export column format in closure.
     * @var array
     */
    public $customExportColumns = [];

    /**
     * Requested records.
     * @var array
     */
    public $data = [];

    /**
     * @param QueryBuilder|EloquentBuilder $query
     * @param array $requestData - data from request by default vklComponents/vklTable/VklTableRequest.php
     *                           - but can use custom request class which must extend vklComponents/dseTable/VklTableRequest.php
     * @param string $resourceClass - Eloquent API Resource class
     */
    public function __construct($query, array $requestData, $resourceClass = null)
    {
        $this->query = $query;

        $this->requestedSearch = $requestData['search'] ?? null;
        $this->requestedOrders = $requestData['order'] ?? [];
        $this->requestedColumns = $requestData['select'] ?? [];
        $this->requestedVisibleColumns = $requestData['visible_columns'] ?? [];
        $this->requestedFilters = $requestData['filter'] ?? [];
        $this->requestedExportType = $requestData['export'] ?? null;
        $this->requestedExportColumnTitles = $requestData['export_column_titles'] ?? [];
        $this->requestedExportColumnFormats = $requestData['export_column_formats'] ?? [];
        $this->requestedLimit = $requestData['limit'] ?? self::DEFAULT_LIMIT;
        $this->requestedPage = $requestData['page'] ?? 1;

        $this->resourceClass = $resourceClass ? $resourceClass : VklTableDefaultResource::class;
    }

    /**
     * Need to execute after all conditions.
     * Apply the query and get the export data.
     *
     * @param boolean $applyRequest
     * @return mixed
     */
    public function resolve($applyRequest = true)
    {
        if ($applyRequest) {
            self::applyRequest();
        }

        // check if pagination is needed
        if ($this->requestedLimit) {
            $this->paginate = $this->query->paginate($this->requestedLimit);

            // if requested page doesn't exist, set last page as requested
            if ($this->requestedPage > $this->paginate->lastPage()) {
                $this->requestedPage = $this->paginate->lastPage();

                // set new number of requested page
                Paginator::currentPageResolver(function() {
                    return $this->requestedPage;
                });

                $this->paginate = $this->query->paginate($this->requestedLimit);
            }

            // apply resource collection
            $this->data = $this->resourceClass::collection($this->paginate->getCollection())->resolve();
        } else {
            // apply resource collection
            $this->data = $this->resourceClass::collection($this->query->get())->resolve();
        }

        if ($applyRequest) {
            self::applySelect();
        }

        // execute action based on requested export type
        return self::executeRequestedExport();
    }

    /**
     * Mapping of column that can be received in the request but should be mapped to other selection when query is executed.
     *
     * ->addCustomColumn('full_name', 'CONCAT(first_name, " ", last_name)')
     *
     * @param string $columnName
     * @param string $query
     * @return $this
     */
    public function addCustomColumn(string $columnName, string $query)
    {
        $this->customColumns[$columnName] = $query;

        return $this;
    }

    /**
     * Set array of column names where we should search.
     * By default all columns are searchable.
     *
     * @param array $searchableColumns
     * @return $this
     */
    public function setSearchableColumns(array $searchableColumns)
    {
        $this->searchableColumns = $searchableColumns;

        return $this;
    }

    /**
     * Set custom order closure for column, to add additional sorting logic.
     * Closure should return query.
     *
     *  ->addCustomOrderColumn('datetime', function ($query, $direction) {
     *      return $query->orderBy('events.start', $direction)
     *          ->orderBy('events_participants.created_at', 'desc');
     *  })
     *
     * @param string $columnName
     * @param Closure $closure - the closure to which the query builder and direction are passed
     *                         - so that order can be arbitrarily changed
     * @return $this
     */
    public function setCustomOrderForColumn(string $columnName, Closure $closure)
    {
        $this->customOrderColumns[$columnName] = $closure;
        return $this;
    }

    /**
     * Set custom filter closure for column, to add additional filter logic.
     *
     * ->setCustomFilterForColumn('status', function ($query, $filterValues) {
     *      foreach ($filterValues as $filterValue) {
     *          switch ($filterValue) {
     *              case $caseValue:
     *                  $query->orWhere('events.is_cancelled', true);
     *                  break;
     *              ...
     *          }
     *      }
     * })
     *
     * @param string $columnName
     * @param Closure $closure - the closure to which the query builder and filter values are passed
     * @return $this
     */
    public function setCustomFilterForColumn(string $columnName, Closure $closure)
    {
        $this->customFilterColumns[$columnName] = $closure;

        return $this;
    }

    /**
     * Set custom format of column data for server table export.
     * Closure should return string.
     *
     * ->addCustomExportColumn('start', function ($record) {
     *      return VklTableHelper::convertBaseDateTimeToAppDateTime($record['start']);
     * })
     *
     * @param string $columnName
     * @param Closure $closure - the closure to which table record array value is passed
     * @return $this
     */
    public function setCustomExportForColumn(string $columnName, Closure $closure)
    {
        $this->customExportColumns[$columnName] = $closure;

        return $this;
    }

    /**
     * Add custom export type with realization in closure.
     * Closure should return response.
     *
     * ->addCustomExportType('custom', function (VklTableBuilder $builder) {
     *      return VklTableHelper::getExcelResponse($builder->data, $builder->getRequestedVisibleColumnTitles());
     * })
     *
     * @param string $exportTypeName
     * @param Closure $closure - the closure that should return exported data response
     * @return $this
     */
    public function addCustomExportType(string $exportTypeName, Closure $closure)
    {
        $this->customExportTypes[$exportTypeName] = $closure;

        return $this;
    }

    /**
     * Get array of column names with headers that should be in export (column => header).
     *
     * @return array
     */
    public function getRequestedVisibleColumnHeaders()
    {
        $columnTitles = [];

        foreach ($this->requestedExportColumnTitles as $requestedExportColumnTitle) {
            $requestedExportColumnTitle = explode(':', $requestedExportColumnTitle, 2);

            $columnTitles[$requestedExportColumnTitle[0]] = $requestedExportColumnTitle[1];
        }

        $requestedVisibleColumnHeaders = [];

        foreach ($this->requestedVisibleColumns as $requestedVisibleColumn) {
            if (array_key_exists($requestedVisibleColumn, $columnTitles)) {
                $requestedVisibleColumnHeaders[$requestedVisibleColumn] = $columnTitles[$requestedVisibleColumn];
            } else {
                $requestedVisibleColumnHeaders[$requestedVisibleColumn] = ucfirst($requestedVisibleColumn);
            }
        }

        return $requestedVisibleColumnHeaders;
    }

    /**
     * Get requested column formats in array (column => format).
     *
     * @return array
     */
    public function getRequestedColumnFormatsInArray()
    {
        $columnFormats = [];

        foreach ($this->requestedExportColumnFormats as $requestedExportColumnFormat) {
            $requestedExportColumnFormat = explode(':', $requestedExportColumnFormat, 2);

            $columnFormats[$requestedExportColumnFormat[0]] = $requestedExportColumnFormat[1];
        }

        return $columnFormats;
    }

    /**
     * Execute action based on requested export type.
     *
     * @return array|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function executeRequestedExport()
    {
        if ($this->requestedExportType) {
            if (array_key_exists($this->requestedExportType, $this->customExportTypes)) {
                return $this->customExportTypes[$this->requestedExportType]($this);
            }

            if ($this->requestedExportType == 'excel') {
                $records = [];

                $requestedVisibleColumnHeaders = $this->getRequestedVisibleColumnHeaders();

                foreach ($this->data as $record) {
                    array_push($records, VklTableHelper::getRecordInExportFormat($record,  array_keys($requestedVisibleColumnHeaders), $this->getRequestedColumnFormatsInArray(), $this->customExportColumns));
                }

                return VklTableHelper::getExcelResponse($records, $requestedVisibleColumnHeaders);
            }
        }

        return self::getTableData();
    }

    /**
     * Get export table data for client table.
     *
     * @return array
     */
    private function getTableData()
    {
        self::setResultValues();

        return $this->result;
    }

    /**
     * Add requested search, order, filter expressions to the query.
     */
    private function applyRequest()
    {
        self::applySearch();
        self::applyOrder();
        self::applyFilter();
    }

    /**
     * Add the requested search expression to the query.
     */
    private function applySearch()
    {
        if ($this->requestedSearch && !empty($this->searchableColumns)) {
            // search only in visible columns
            if ($this->requestedVisibleColumns) {
                $this->searchableColumns = array_intersect($this->searchableColumns, $this->requestedVisibleColumns);
            }

            $query = $this->query->where(function ($query) {
                foreach ($this->searchableColumns as $columnName) {
                    // check if column is custom
                    if (array_key_exists($columnName, $this->customColumns)) {
                        $query->orWhereRaw($this->customColumns[$columnName] . " like ?", ["%{$this->requestedSearch}%"]);
                    } else {
                        $query->orWhere($columnName, 'like', "%{$this->requestedSearch}%");
                    }
                }
            });

            $this->query = $query;
        }
    }

    /**
     * Add the requested order expression to the query.
     */
    private function applyOrder()
    {
        if (!empty($this->requestedOrders)) {
            foreach ($this->requestedOrders as $order) {
                $order = explode(':', $order, 2);

                $columnName = $order[0];

                // get order direction, set asc by default if direction is not specified
                $direction = in_array($order[1], ['asc', 'desc']) ? $order[1] : 'asc';

                // check if order is custom
                if (array_key_exists($columnName, $this->customOrderColumns)) {
                    $this->query = $this->customOrderColumns[$columnName]($this->query, $direction);
                } else {
                    // check if column is custom
                    if (array_key_exists($columnName, $this->customColumns)) {
                        $this->query = $this->query->orderByRaw($this->customColumns[$columnName] . ' ' . $direction);
                    } else {
                        $this->query = $this->query->orderBy($columnName, $direction);
                    }
                }
            }
        }
    }

    /**
     * Add the requested filter conditions to the query.
     */
    private function applyFilter()
    {
        if (!empty($this->requestedFilters)) {
            foreach ($this->requestedFilters as $filter) {
                $filter = explode(':', $filter, 2);

                $columnName = $filter[0];

                $filterValues = explode(',', $filter[1]);

                // check if filter is custom
                if (array_key_exists($columnName, $this->customFilterColumns)) {
                    $this->query->where(function ($query) use ($columnName, $filterValues) {
                        $this->customFilterColumns[$columnName]($query, $filterValues);
                    });
                } else {
                    // check if column is custom
                    if (array_key_exists($columnName, $this->customColumns)) {
                        // get (?, ?..) block for 'where in (?, ?...)' sql query
                        $block = '?';

                        $count = count($filterValues);

                        for ($i = 1; $i < $count; $i++) {
                            $block .= ', ?';
                        }

                        $this->query = $this->query->whereRaw($this->customColumns[$columnName] . ' in (' . $block . ')', [$filterValues]);
                    } else {
                        $this->query = $this->query->whereIn($columnName, $filterValues);
                    }
                }
            }
        }
    }

    /**
     * Leave only selected columns in the data.
     */
    private function applySelect()
    {
        if (!empty($this->requestedColumns)) {
            $selectedKeys = array_flip($this->requestedColumns);

            foreach ($this->data as $key => $item) {
                // get only selected keys
                $this->data[$key] = array_intersect_key($item, $selectedKeys);
            }
        }
    }

    /**
     * Set result values based on requested table data.
     */
    private function setResultValues()
    {
        $this->result = [
            'data' => $this->data
        ];

        if ($this->paginate) {
            $this->result = array_merge($this->result, [
                'amount' => $this->paginate->total(),
                'perPage' => $this->paginate->perPage(),
                'currentPage' => $this->paginate->currentPage(),
                'lastPage' => $this->paginate->lastPage()
            ]);
        }
    }
}