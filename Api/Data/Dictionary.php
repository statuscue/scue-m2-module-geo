<?php

namespace Scue\Geo\Api\Data;

interface Dictionary
{
    public function getName(): ?string;
    public function getId(): string|int;
    public function getArea(): ?string;
}