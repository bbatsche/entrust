<?php

namespace Bbatsche\Entrust;

/**
 * Maintaining this trait for backwards compatibility.
 *
 * @deprecated Use \Bbatsche\Entrust\Traits\EntrustUserTrait instead
 *             as it fits with standard naming convention
 *
 * @see \Bbatsche\Entrust\Traits\EntrustUserTrait
 */
trait HasRole
{
    use \Bbatsche\Entrust\Traits\EntrustUserTrait;
}
