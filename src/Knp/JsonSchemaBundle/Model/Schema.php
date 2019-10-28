<?php

namespace Knp\JsonSchemaBundle\Model;

class Schema implements \JsonSerializable
{
    const TYPE_OBJECT = 'object';
    const SCHEMA_V4 = 'http://json-schema.org/draft-04/schema#';
    const SCHEMA_V6 = 'http://json-schema.org/draft-06/schema#';
    const SCHEMA_V7 = 'http://json-schema.org/draft-07/schema#';

    private $title;
    private $id;
    private $type;
    private $schema;
    private $properties;
    private $additionalProperties;
    private $groups;

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function addProperty(Property $property)
    {
        $name = $property->getDisplayName() ?: $property->getName();
        $this->properties[$name] = $property;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    public function setAdditionalProperties($additional)
    {
        $this->additionalProperties = $additional;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    public function jsonSerialize()
    {
        $properties = array_filter($this->properties, function ($property) {
            return empty($this->groups) || !empty(array_intersect($this->groups, $property->getGroups()));
        });

        $serialized = array(
            'title'                                              => $this->title,
            'type'                                               => $this->type,
            '$schema'                                            => $this->schema,
            (Schema::SCHEMA_V4 === $this->schema ? 'id' : '$id') => $this->id,
            'properties'                                         => $properties,
            'additionalProperties'                               => $this->additionalProperties,
        );

        $requiredProperties = array_keys(array_filter($properties, function ($property) {
            return $property->isRequired();
        }));

        if (count($requiredProperties) > 0) {
            $serialized['required'] = $requiredProperties;
        }

        return $serialized;
    }
}
