<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Models;

use Skeleton\Models\Traits\SoftDeletes;
use Skeleton\Models\Traits\Timestamps;
use PDO;
use Skeleton\Singletons\ConnectionSingleton;
use Skeleton\Database\QueryBuilder;
use Skeleton\Singletons\QueryBuilderSingleton;

class Model
{
    use SoftDeletes;
    use Timestamps;
    public static $table;

    /**
     * The database connection.
     */
    public static PDO $db;

    /**
     * @var array $hidden The attributes that should be hidden from the JSON and array representations of the model
     */
    protected $hidden = [];

    /**
     * The name of the primary key column.
     *
     * @var string
     */
    public static $primaryKey = 'id';

    /**
     * The query builder instance.
     */
    public static QueryBuilder $queryBuilder;

    /**
     * Set the database connection for the model.
     *
     * @param PDO $db The database connection to use.
     */
    public static function setDb(PDO $db): void
    {
        static::$db = $db;
        static::$queryBuilder = new QueryBuilder($db);
    }

    /**
     * Constructor
     *
     * @param array|null $attributes The model's attributes
     */
    public function __construct(public ?array $attributes = null)
    {
        static::$db = ConnectionSingleton::getInstance()->getConnection();
        static::$queryBuilder = QueryBuilderSingleton::getInstance()->getQueryBuilder();
        self::$primaryKey = static::$primaryKey;
        
        // Ensure attributes is always an array
        if ($this->attributes === null) {
            $this->attributes = [];
        }
    }

    /**
     * Magic getter for accessing model attributes
     *
     * @param string $key The attribute to get
     * @return mixed The value of the attribute, or null if the attribute does not exist
     */
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Magic setter for setting model attributes
     *
     * @param string $key The attribute to set
     * @param mixed $value The value to set for the attribute
     */
    public function __set($key, mixed $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns an array representation of the model's attributes
     *
     * @return array The model's attributes, with any attributes in the $hidden array excluded
     */
    public function toArray(): array
    {
        $attributes = $this->attributes;
        foreach ($this->hidden as $key) {
            unset($attributes[$key]);
        }

        return $attributes;
    }

    /**
     * Returns a JSON representation of the model's attributes
     *
     * @param int $options Options for json_encode
     * @return string The JSON representation of the model's attributes, with any attributes in the $hidden array excluded
     */
    public function toJson(int $options = 0): string
    {
        $attributes = $this->toArray();
        return json_encode($attributes, $options);
    }

    public static function getTable(): string
    {
        return static::$table;
    }


    /**
     * Returns all of the model's attributes
     *
     * @return array The model's attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Find a record by its primary key.
     *
     * @param int $id The primary key of the record to find.
     *
     * @return mixed The found record, or null if no record was found.
     */
    public static function find(int $id): ?self
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->select()
            ->from(static::$table)
            ->where(static::$table . '.' . 'id', '=', $id)
            ->limit(1);

        $stmt = static::$queryBuilder->getPDOStatement();
        $stmt->execute();

        $result = $stmt->fetch();

        if ($result) {
            return new static($result);
        }

        return null;
    }

    /**
     * Find all records in the table.
     *
     * @return array An array of all the records in the table.
     */
    public static function all(): array
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->select()
            ->from(static::$table);

        $stmt = static::$queryBuilder->getPDOStatement();
        $stmt->execute();

        $results = $stmt->fetchAll();

        $objects = [];
        foreach ($results as $result) {
            $objects[] = new static($result);
        }

        return $objects;
    }

    /**
     * Insert a new record into the table.
     *
     * @param array $attributes The attributes for the new record.
     *
     * @return bool Whether the insert was successful.
     */
    public static function insert(array $attributes): string|false
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->insert($attributes)
            ->into(static::$table);

        $stmt = static::$queryBuilder->getPDOStatement();
        return $stmt->execute() ? static::$queryBuilder->lastInsertId() : false;
    }

    /**
     * Update a record in the database.
     *
     * @param array $attributes The attributes to update.
     *
     */
    public function update(array $attributes): bool
    {
        static::$queryBuilder->reset();
        unset($attributes[static::$primaryKey]);
        static::$queryBuilder->update($attributes)
            ->table(static::$table)
            ->where(static::$primaryKey, '=', $this->{static::$primaryKey});

        $stmt = static::$queryBuilder->getPDOStatement();
        return $stmt->execute();
    }

    public static function delete(int $id): bool
    {
        static::$queryBuilder->reset();
        if (static::hasSoftDeletes()) {
            $timestamps = ['deleted_at' => date('Y-m-d H:i:s')];
            static::$queryBuilder->update($timestamps)
                ->table(static::$table)
                ->where(static::$primaryKey, '=', $id);

            $stmt = static::$queryBuilder->getPDOStatement();
            return $stmt->execute();
        }

        static::$queryBuilder->delete()
            ->from(static::$table)
            ->where('id', '=', $id);
        $stmt = static::$queryBuilder->getPDOStatement();
        return $stmt->execute();
    }

    public function remove(): void
    {
        static::$queryBuilder->reset();
        $table = static::getTable();
        $query = static::$queryBuilder->delete()->from($table)->where(static::$primaryKey, '=', $this->attributes[static::$primaryKey]);
        $stmt = $query->getPDOStatement();
        $stmt->execute();
    }


    public static function count(): int|string|false|null
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->select()
            ->count()
            ->from(static::$table);

        return static::$queryBuilder->fetchColumn(0);
    }

    public static function exists(int $id): bool
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->select()
            ->count()
            ->from(static::$table)
            ->where('id', '=', $id);

        return static::$queryBuilder->fetchColumn(0) > 0;
    }

    public static function max(string $column): int|string|false|null
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->select()
            ->max($column)
            ->from(static::$table);

        return static::$queryBuilder->fetchColumn(0);
    }

    /**
     * Get the minimum value of a column in the table.
     *
     * @param string $column The column to get the minimum value for.
     *
     * @return mixed The minimum value of the column, or null if the table is empty.
     */
    public static function min(string $column): int|string|false|null
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->table(static::$table)
            ->min($column);

        return static::$queryBuilder->fetchColumn(0);
    }

    /**
     * Get the average value of a column in the table.
     *
     * @param string $column The column to get the average value for.
     *
     * @return float The average value of the column, or null if the table is empty.
     */
    public static function avg(string $column): int|string|false|null
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->table(static::$table)
            ->avg($column);

        return static::$queryBuilder->fetchColumn(0);
    }

    public function hasOne(string $related, ?string $foreignKey = null, ?string $localKey = null)
    {
        static::$queryBuilder->reset();

        // Set default foreign and local keys if not provided
        if ($foreignKey === null) {
            $foreignKey = strtolower(static::getTable()) . '_id';
        }

        if ($localKey === null) {
            $localKey = 'id';
        }

        // Get the name of the related table
        $relatedTable = $related::getTable();

        // Build the query
        $query = static::$queryBuilder
            ->select()
            ->from($relatedTable)
            ->where($relatedTable . '.' . $foreignKey, '=', $this->attributes[$localKey]);

        // Execute the query and fetch the result
        $stmt = $query->getPDOStatement();
        $stmt->execute();

        $result = $stmt->fetch();

        // Create a related model instance
        $relatedModel = new $related($result);

        // Return the related model
        return $relatedModel;
    }

    public function hasOneThrough($relatedModel, $throughModel, ?string $foreignKey = null, ?string $throughKey = null, ?string $localKey = null)
    {
        static::$queryBuilder->reset();
        // Set default foreign and through keys if not provided
        if ($foreignKey === null) {
            $foreignKey = strtolower((new $relatedModel())->getTable()) . '_id';
        }

        if ($throughKey === null) {
            $throughKey = strtolower((new $throughModel())->getTable()) . '_id';
        }

        // Get the names of the related and through tables
        $relatedTable = $relatedModel::getTable();
        $throughTable = $throughModel::getTable();

        // Build the query
        $query = static::$queryBuilder
            ->select()
            ->from($relatedTable)
            ->join($throughTable, $relatedTable . '.' . $foreignKey, '=', $throughTable . '.' . $throughKey)
            ->where($throughTable . '.' . $localKey, '=', $this->attributes[static::$primaryKey]);

        // Execute the query and fetch the results
        $stmt = $query->getPDOStatement();
        $stmt->execute();

        $result = $stmt->fetch();

        // Create an related model instance
        $relatedModel = new $relatedModel($result);

        // Return the related model
        return $relatedModel;
    }

    /**
     * @return object[]
     */
    public function hasMany(string $related, ?string $foreignKey = null, ?string $localKey = null): array
    {
        static::$queryBuilder->reset();

        // Set default foreign and local keys if not provided
        if ($foreignKey === null) {
            $foreignKey = strtolower((new $related())->getTable()) . '_id';
        }

        if ($localKey === null) {
            $localKey = strtolower(static::getTable()) . '_id';
        }

        // Get the name of the related table
        $relatedTable = $related::getTable();

        // Build the query
        $query = static::$queryBuilder
            ->select()
            ->from($relatedTable)
            ->where($relatedTable . '.' . $foreignKey, '=', $this->attributes[$localKey]);

        // Execute the query and fetch the results
        $stmt = $query->getPDOStatement();
        $stmt->execute();

        $results = $stmt->fetchAll();

        // Create an array of related model instances
        $relatedModels = [];
        foreach ($results as $result) {
            $relatedModels[] = new $related($result);
        }

        // Return the array of related models
        return $relatedModels;
    }

    /**
     * @return object[]
     */
    public function hasManyThrough($relatedModel, $throughModel, ?string $foreignKey = null, ?string $throughKey = null, ?string $localKey = null): array
    {
        static::$queryBuilder->reset();
        // Set default foreign and through keys if not provided
        if ($foreignKey === null) {
            $foreignKey = strtolower((new $relatedModel())->getTable()) . '_id';
        }

        if ($throughKey === null) {
            $throughKey = strtolower((new $throughModel())->getTable()) . '_id';
        }

        // Get the names of the related and through tables
        $relatedTable = $relatedModel::getTable();
        $throughTable = $throughModel::getTable();

        // Build the query
        $query = static::$queryBuilder
            ->select()
            ->from($relatedTable)
            ->join($throughTable, $relatedTable . '.' . $foreignKey, '=', $throughTable . '.' . $throughKey)
            ->where($throughTable . '.' . $localKey, '=', $this->attributes[static::$primaryKey]);

        // Execute the query and fetch the results
        $stmt = $query->getPDOStatement();
        $stmt->execute();

        $results = $stmt->fetchAll();

        // Create an array of related model instances
        $relatedModels = [];
        foreach ($results as $result) {
            $relatedModels[] = new $relatedModel($result);
        }

        // Return the array of related models
        return $relatedModels;
    }

    /**
     * Get the sum of the values of a column in the table.
     *
     * @param string $column The column to get the sum for.
     *
     * @return int The sum of the values of the column.
     */
    public static function sum(string $column): int|string|false|null
    {
        static::$queryBuilder->reset();
        static::$queryBuilder->table(static::$table)
            ->sum($column);

        return static::$queryBuilder->fetchColumn(0);
    }

    public static function where(array $conditions): QueryBuilder
    {
        static::$queryBuilder->reset();
        $query = static::$queryBuilder->select()->from(static::$table);

        foreach ($conditions as $condition) {
            [$column, $operator, $value] = $condition;
            $query->where($column, $operator, $value);
        }

        return $query;
    }


    public static function whereIn(string $column, array $values): QueryBuilder
    {
        static::$queryBuilder->reset();

        return static::$queryBuilder->select()->from(static::$table)->whereIn($column, $values);
    }

    /**
     * Retrieves a model object with a given attribute value, or creates a new model object with the given attribute value if one does not exist
     *
     * @param array $attributes The attributes to check for existing record
     * @param array $createAttributes The attributes to set for the new model object if one is created
     * @return static The model object with the given attribute value
     */
    public static function firstOrCreate(array $attributes, array $createAttributes): static
    {
        static::$queryBuilder->reset();
        $model = static::where($attributes)->first();
        if (!$model) {
            $model = new static($createAttributes);
            $model->save();
            return $model;
        }

        return new static($model);
    }

    /**
     * Retrieves a model object with a given attribute value, or creates a new model object with the given attribute value if one does not exist
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to check for
     * @param array $attributes The attributes to set for the new model object if one is created
     * @return Model The model object with the given attribute value
     */
    public static function create($attributes): static
    {
        static::$queryBuilder->reset();
        $model = new static($attributes);
        $model->save();
        $model->attributes['id'] = static::$queryBuilder->lastInsertId();
        return $model;
    }

    public function save()
    {
        if (isset($this->attributes[static::$primaryKey])) {
            // If primary key is set, update record
            if (self::hasTimestamps()) {
                $this->attributes['updated_at'] = date('Y-m-d H:i:s');
            }

            return static::update((array) $this->attributes);
        }

        // If primary key is not set, insert new record
        if (self::hasTimestamps()) {
            $this->attributes['created_at'] = date('Y-m-d H:i:s');
            $this->attributes['updated_at'] = date('Y-m-d H:i:s');
        }

        return static::insert((array)$this->attributes);
    }

    /**
     * Check if the model has the "Timestamps" trait.
     *
     * @return bool True if the model has the "Timestamps" trait, false otherwise.
     */
    public static function hasTimestamps(): bool
    {
        $traits = class_uses(static::class);
        return in_array(Timestamps::class, $traits);
    }

    /**
     * Check if the model has the "SoftDeletes" trait.
     *
     * @return bool True if the model has the "SoftDeletes" trait, false otherwise.
     */
    public static function hasSoftDeletes(): bool
    {
        $traits = class_uses(static::class);
        return in_array(SoftDeletes::class, $traits);
    }

    public function get_object_vars(): array
    {
        return get_object_vars($this);
    }
}
