<?php namespace Mohsin\Mobile\Classes;

use ApplicationException;
use System\Classes\ModelBehavior;

/**
 * Variant model extension
 *
 * Usage:
 *
 * In the model class definition:
 *
 *   public $implement = ['Mohsin.Mobile.Behaviors.VariantModel'];
 *
 */
class VariantModel extends ModelBehavior
{
    protected $parent;

    /**
     * {@inheritDoc}
     */
    protected $requiredProperties = ['belongsTo'];

    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->parent = $parent;

        if (!array_key_exists('variant', $parent -> belongsTo)) {
            throw new ApplicationException('Ensure that ' . get_class($parent) . ' contains variant in belongsTo relation array.');
        }
    }

    public function scopeWithVariant($query, $variant_id)
    {
        return $query->where('variant_id', $variant_id);
    }
}
