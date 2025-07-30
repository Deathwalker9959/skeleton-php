<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Database;

use PDO;
use PDOStatement;

class QueryBuilder
{
    /**
     * The table to perform the query on.
     */
    protected string $table;

    /**
     * The columns to select.
     */
    protected array $select = [];

    /**
     * The operation is a delete operation.
     *
     * @var boolean
     */
    protected $delete = false;

    /**
     * The columns to insert.
     */
    protected array $insert = [];

    /**
     * The columns to update.
     */
    protected array $update = [];

    /**
     * The WHERE conditions for the query.
     */
    protected array $where = [];

    /**
     * The LIMIT for the query.
     */
    protected int $limit = 0;

    /**
     * The ORDER BY clause for the query.
     */
    protected array $orderBy = [];

    /**
     * The GROUP BY clause for the query.
     */
    protected array $groupBy = [];

    /**
     * The HAVING clause for the query.
     */
    protected array $having = [];

    /**
     * The JOIN clause for the query.
     */
    protected array $join = [];

    /**
     * The values to bind to the query.
     */
    protected array $bindings = [];

    /**
     * Create a new query builder instance.
     *
     * @param PDO $pdo The PDO instance to use.
     */
    public function __construct(
        /**
         * The PDO instance.
         */
        protected PDO $pdo
    )
    {
    }

    public function reset(): void
    {
        unset($this->table);
        $this->select = [];
        $this->delete = false;
        $this->insert = [];
        $this->update = [];
        $this->where = [];
        $this->limit = 0;
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->join = [];
        $this->bindings = [];
    }

    /**
     * Set the table to perform the query on.
     *
     * @param string $table The table to perform the query on.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function table(string $table): QueryBuilder
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the columns to select.
     *
     * @param array $columns The columns to select.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function select(array $columns = ['*']): QueryBuilder
    {
        $this->select = $columns;

        return $this;
    }

    /**
     * Set the columns to insert.
     *
     * @param array $values The columns to insert.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function insert(array $values): QueryBuilder
    {
        $this->insert = array_keys($values);
        $this->bindings = array_values($values);
        return $this;
    }

    /**
     * Set the values to update in the database.
     *
     * @param array $values The values to update.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function update(array $values): QueryBuilder
    {
        $this->update = array_keys($values);
        $this->bindings = array_values($values);
        return $this;
    }

    /**
     * Add a WHERE clause to the query.
     *
     * @param string $column The column to compare.
     * @param string $operator The operator to use for the comparison. Default is '='.
     * @param mixed $value The value to compare with.
     * @param string|null $boolean The boolean operator to use. Default is 'and'.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function where(string $column, string $operator, mixed $value, ?string $boolean = null): QueryBuilder
    {
        $this->where[] = ['column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => $boolean];
        $this->bind($value);

        return $this;
    }

    /**
     * Add a WHERE IN clause to the query.
     *
     * @param string $column The column to compare.
     * @param array $values The values to compare with.
     * @param string|null $boolean The boolean operator to use.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function whereIn(string $column, array $values, ?string $boolean = null): QueryBuilder
    {
        $this->where[] = ['column' => $column, 'values' => $values, 'boolean' => $boolean];
        $this->bindArray(['values' => $values]);

        return $this;
    }

    /**
     * Add a LIMIT clause to the query.
     *
     * @param int $limit The limit for the query.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Add an ORDER BY clause to the query.
     *
     * @param string $column The column to order by.
     * @param string $direction The direction to order in.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function orderBy(string $column, string $direction = 'asc'): QueryBuilder
    {
        $this->orderBy[] = ['column' => $column, 'direction' => $direction];

        return $this;
    }

    /**
     * Add a GROUP BY clause to the query.
     *
     * @param string $column The column to group by.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function groupBy(string $column): QueryBuilder
    {
        $this->groupBy[] = $column;

        return $this;
    }

    /**
     * Add a HAVING clause to the query.
     *
     * @param string $column The column to compare.
     * @param string $operator The operator to use for the comparison
     * @param mixed $value The value to compare with.
     * @param string $boolean The boolean operator to use.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function having(string $column, string $operator, mixed $value, string $boolean = 'and'): QueryBuilder
    {
        $this->having[] = ['column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => $boolean];
        $this->bind($value);

        return $this;
    }

    /**
     * Add a JOIN clause to the query.
     *
     * @param string $table The table to join.
     * @param string $first The first column to join on.
     * @param string $operator The operator to use for the join.
     * @param string $second The second column to join on.
     * @param string $type The type of join to perform.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'inner'): QueryBuilder
    {
        $this->join[] = ['table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second, 'type' => $type];

        return $this;
    }

    /**
     * Bind a value to the query.
     *
     * @param mixed $value The value to bind.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function bind(mixed $value): QueryBuilder
    {
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Bind an array of values to the query.
     *
     * @param array $values The values to bind.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function bindArray(array $values): QueryBuilder
    {
        foreach ($values as $value) {
            $this->bind($value);
        }

        return $this;
    }

    /**
     * Set the SELECT clause to count rows.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function count(): QueryBuilder
    {
        $this->select = ['COUNT(*)'];

        return $this;
    }

    /**
     * Set the SELECT clause to get the minimum value of a column.
     *
     * @param string $column The column to get the minimum value for.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function min(string $column): QueryBuilder
    {
        $this->select = [sprintf('MIN(%s)', $column)];

        return $this;
    }

    /**
     * Set the SELECT clause to get the maximum value of a column.
     *
     * @param string $column The column to get the maximum value for.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function max(string $column): QueryBuilder
    {
        $this->select = [sprintf('MAX(%s)', $column)];

        return $this;
    }

    /**
     * Set the SELECT clause to get the average value of a column.
     *
     * @param string $column The column to get the average value for.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function avg(string $column): QueryBuilder
    {
        $this->select = [sprintf('AVG(%s)', $column)];

        return $this;
    }

    /**
     * Set the SELECT clause to get the sum of a column.
     *
     * @param string $column The column to get the sum for.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function sum(string $column): QueryBuilder
    {
        $this->select = [sprintf('SUM(%s)', $column)];

        return $this;
    }

    /**
     * Set the FROM clause of the query.
     *
     * @param string $table The table to select from.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function from(string $table): QueryBuilder
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the table to insert into.
     *
     * @param string $table The table to insert into.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function into(string $table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Fetch a single column from the result set.
     *
     * @param int $column The column index to fetch.
     *
     * @return int|string|false|null The value of the column.
     */
    public function fetchColumn(int $column): int|string|false|null
    {
        $stmt = $this->getPDOStatement();
        $stmt->execute();
        return $stmt->fetchColumn($column);
    }

    /**
     * Execute the query and return the result.
     *
     * @return mixed The result of the query.
     */
    public function execute(): array|int
    {
        $sql = $this->toSql();
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($this->bindings);

        $this->reset();

        if (preg_match('/^(select|describe|pragma)/i', $sql)) {
            return $stmt->fetchAll();
        }

        return $stmt->rowCount();
    }

    /**
     * Execute the SELECT query and return the results.
     *
     * @return array The selected rows.
     */
    public function get(): array
    {
        $stmt = $this->getPDOStatement();
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Retrieves the first result from the query
     *
     * @return array The first result from the query as an associative array
     */
    public function first(): mixed
    {
        $this->select()->limit(1);
        $stmt = $this->getPDOStatement();
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Set the DELETE clause of the query.
     *
     * @return QueryBuilder This query builder instance.
     */
    public function delete(): QueryBuilder
    {
        $this->delete = true;

        return $this;
    }

    public function postProcessWhere(): void
    {
        if (($this->where === []) >= 2) {
            return;
        }

        $boolean = "AND";
        $counter = count($this->where);
        for ($i = 0; $i < $counter; $i++) {
            if ($this->where[$i]['boolean'] === "") {
                $this->where[$i]['boolean'] = $boolean;
            }
        }
    }


    /**
     * Convert the query builder to a SQL string.
     *
     * @return string The SQL string for the query.
     */
    public function toSql(): string
    {
        $sql = '';
        $this->postProcessWhere();

        if ($this->select !== []) {
            $columns = implode(', ', $this->select);

            $sql .= sprintf('SELECT %s FROM %s', $columns, $this->table);
        }

        if ($this->insert !== []) {
            $columns = implode(', ', $this->insert);
            $values = implode(', ', array_fill(0, count($this->insert), '?'));

            $sql .= sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table, $columns, $values);
        }

        if ($this->delete) {
            $sql .= 'DELETE FROM ' . $this->table;
        }

        if ($this->update !== []) {
            $sql .= sprintf('UPDATE %s SET ', $this->table);
            foreach ($this->update as $column) {
                $sql .= $column . ' = ?, ';
            }

            $sql = rtrim($sql, ', ');
        }

        foreach ($this->join as $join) {
            $sql .= sprintf(' %s JOIN %s ON %s %s %s', $join['type'], $join['table'], $join['first'], $join['operator'], $join['second']);
        }

        if ($this->where !== []) {
            $sql .= ' WHERE';

            foreach ($this->where as $i => $where) {
                if ($i > 0) {
                    $sql .= ' ' . $where['boolean'];
                }

                if (isset($where['values'])) {
                    $sql .= sprintf(' %s IN (', $where['column']) . implode(', ', array_fill(0, count($where['values']), '?')) . ')';
                } else {
                    $sql .= sprintf(' %s %s ?', $where['column'], $where['operator']);
                }
            }
        }

        if ($this->groupBy !== []) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if ($this->having !== []) {
            $sql .= ' HAVING';

            foreach ($this->having as $having) {
                if (isset($having['values'])) {
                    $sql .= sprintf(' %s %s IN (', $having['boolean'], $having['column']) . implode(', ', array_fill(0, count($having['values']), '?')) . ')';
                } else {
                    $sql .= sprintf(' %s %s %s ?', $having['boolean'], $having['column'], $having['operator']);
                }
            }
        }

        if ($this->orderBy !== []) {
            $sql .= ' ORDER BY';

            foreach ($this->orderBy as $orderBy) {
                $sql .= sprintf(' %s %s,', $orderBy['column'], $orderBy['direction']);
            }

            $sql = rtrim($sql, ',');
        }

        if ($this->limit !== 0) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        return $sql;
    }

    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Get the bindings for the query.
     *
     * @return array The bindings for the query.
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function getPDOStatement(): PDOStatement
    {
        $sql = $this->toSql();
        $stmt = $this->pdo->prepare($sql);

        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }

        return $stmt;
    }
}
