<?php
declare(strict_types=1);

namespace App\Shared\Domain\Collection;

/**
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-extends \IteratorAggregate<TKey, T>
 */
class Collection implements \IteratorAggregate
{
    protected array $elements = [];

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    public function add(object $element): void
    {
        $this->elements[] = $element;
    }

    public function contains($element): bool
    {
        return in_array($element, $this->elements, true);
    }

    public function removeElement(object $element): void
    {
        $key = array_search($element, $this->elements, true);
        if ($key !== false) {
            unset($this->elements[$key]);
        }
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function filter(\Closure $p): self
    {
        $elements = array_filter($this->elements, $p);
        $collection = new self();
        foreach ($elements as $element) {
            $collection->add($element);
        }

        return $collection;
    }
}
