<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Kafka;

use longlang\phpkafka\Consumer\ConsumeMessage;

abstract class AbstractConsumer
{
    /**
     * @var string
     */
    public $name = 'kafka';

    /**
     * @var string
     */
    public $pool = 'default';

    /**
     * @var string
     */
    public $topic;

    /**
     * @var null|string
     */
    public $groupId;

    /**
     * @var null|string
     */
    public $memberId;

    /**
     * @var null|string
     */
    public $groupInstanceId;

    /**
     * @var bool
     */
    public $autoCommit = true;

    public function getPool(): string
    {
        return $this->pool;
    }

    public function setPool(string $pool): void
    {
        $this->pool = $pool;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function setGroupId(?string $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function getMemberId(): ?string
    {
        return $this->memberId;
    }

    public function setMemberId(?string $memberId): void
    {
        $this->memberId = $memberId;
    }

    public function getGroupInstanceId(): ?string
    {
        return $this->groupInstanceId;
    }

    public function setGroupInstanceId(?string $groupInstanceId): void
    {
        $this->groupInstanceId = $groupInstanceId;
    }

    public function isAutoCommit(): bool
    {
        return $this->autoCommit;
    }

    public function setAutoCommit(bool $autoCommit): void
    {
        $this->autoCommit = $autoCommit;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    abstract public function consume(ConsumeMessage $message): string;
}
