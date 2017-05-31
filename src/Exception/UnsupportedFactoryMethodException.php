<?php

namespace Sylius\ElasticSearchPlugin\Exception;

final class UnsupportedFactoryMethodException extends \RuntimeException
{
    public function __construct($methodName, $reason, \Exception $previousException = null)
    {
        parent::__construct(sprintf('This method "%s" is not supported. %s', $methodName, $reason), 0, $previousException);
    }
}
