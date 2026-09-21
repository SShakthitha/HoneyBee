<?php

namespace App\Models\Concerns;

trait HasOfferPricing
{
    public function hasValidOfferPrice(): bool
    {
        $price = (float) ($this->price ?? 0);
        $offer = $this->offer_price ?? null;

        if ($offer === null || $offer === '' || $offer === '0' || (float) $offer <= 0) {
            return false;
        }

        return (float) $offer < $price;
    }

    public function currentSellingPrice(): float
    {
        return $this->hasValidOfferPrice() ? (float) $this->offer_price : (float) ($this->price ?? 0);
    }

    public function discountPercentage(): int
    {
        if (! $this->hasValidOfferPrice()) {
            return 0;
        }

        $price = (float) ($this->price ?? 0);
        $offer = (float) $this->offer_price;

        if ($price <= 0) {
            return 0;
        }

        $percent = (($price - $offer) / $price) * 100;

        return max(0, (int) round($percent));
    }
}
