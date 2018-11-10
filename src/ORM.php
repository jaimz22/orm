<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\ORM;

use Spiral\Database\DatabaseInterface;
use Spiral\Database\DatabaseManager;

/**
 * Central class ORM, provides access to various pieces of the system and manages schema state.
 */
class ORM implements ORMInterface
{
    // Memory section to store ORM schema.
    const MEMORY = 'orm.schema';

    /** @var DatabaseManager */
    private $dbal;

    /** @var SchemaInterface */
    private $schema;

    /** @var FactoryInterface */
    private $factory;

    /** @var null|HeapInterface */
    private $heap = null;

    /** @var MapperInterface[] */
    private $mappers = [];

    /** @var RelationMap[] */
    private $relmaps = [];

    /**
     * @param DatabaseManager       $dbal
     * @param FactoryInterface|null $factory
     */
    public function __construct(DatabaseManager $dbal, FactoryInterface $factory = null)
    {
        $this->dbal = $dbal;
        $this->factory = $factory;
        $this->heap = new Heap();
    }

    /**
     * @inheritdoc
     */
    public function getDatabase(string $class): DatabaseInterface
    {
        return $this->dbal->database(
            $this->getSchema()->define($class, Schema::DATABASE)
        );
    }

    /**
     * @inheritdoc
     */
    public function getMapper(string $class): MapperInterface
    {
        if (isset($this->mappers[$class])) {
            return $this->mappers[$class];
        }

        return $this->mappers[$class] = $this->getFactory()->mapper($class);
    }

    /**
     * Get relation map associated with the given class.
     *
     * @param string $class
     * @return RelationMap
     */
    public function getRelationMap(string $class): RelationMap
    {
        if (isset($this->relmaps[$class])) {
            return $this->relmaps[$class];
        }

        $relations = [];

        $names = array_keys($this->getSchema()->define($class, Schema::RELATIONS));
        foreach ($names as $relation) {
            $relations[$relation] = $this->getFactory()->relation($class, $relation);
        }

        return $this->relmaps[$class] = new RelationMap($this, $relations);
    }

    /**
     * @inheritdoc
     */
    public function withSchema(SchemaInterface $schema): ORMInterface
    {
        $orm = clone $this;
        $orm->schema = $schema;
        $orm->factory = $orm->factory->withContext($orm, $orm->schema);

        return $orm;
    }

    /**
     * @inheritdoc
     */
    public function getSchema(): SchemaInterface
    {
        if (empty($this->schema)) {
            $this->schema = $this->loadSchema();
            $this->factory = $this->factory->withContext($this, $this->schema);
        }

        return $this->schema;
    }

    /**
     * @inheritdoc
     */
    public function withFactory(FactoryInterface $factory): ORMInterface
    {
        $orm = clone $this;
        $orm->factory = $factory->withContext($orm, $orm->schema);

        return $orm;
    }

    /**
     * @inheritdoc
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * @inheritdoc
     */
    public function withHeap(HeapInterface $heap): ORMInterface
    {
        $orm = clone $this;
        $orm->heap = $heap;
        $orm->factory = $orm->factory->withContext($orm, $orm->schema);

        return $orm;
    }

    /**
     * @inheritdoc
     */
    public function getHeap(): HeapInterface
    {
        return $this->heap;
    }

    /**
     * @inheritdoc
     */
    public function makeEntity(string $class, array $data, int $state = State::NEW)
    {
        if ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        }

        // locate already loaded entity reference
        if ($this->heap !== null && $state !== State::NEW) {
            $entityID = $this->identify($class, $data);

            // todo: cache it later with path
            // if (!empty($entityID) && $this->heap->has($class, $entityID)) {
            //     return $this->heap->get($class, $entityID);
            // }
        }

        // init the entity
        $entity = $this->getMapper($class)->make(
            $this->getRelationMap($class)->init($data)
        );

        // todo: recursive records are possible using secondary check

        if (!empty($entityID)) {
            // todo: relation data?
            $this->heap->attach($entity, new State($entityID, $state, $data));
        }

        // init relation binding ?

        return $entity;
    }

    /**
     * Reset related objects cache.
     */
    public function __clone()
    {
        $this->mappers = [];
        $this->relmaps = [];
    }

    /**
     * Return value to uniquely identify given entity data. Most likely PrimaryKey value.
     *
     * @param string $class
     * @param array  $data
     * @return string|int|null
     */
    protected function identify(string $class, array $data)
    {
        $pk = $this->getSchema()->define($class, Schema::PRIMARY_KEY);
        if (isset($data[$pk])) {
            return $data[$pk];
        }

        return null;
    }


    protected function loadSchema(): SchemaInterface
    {
        return new Schema([
            // hahahaha
        ]);
    }
}