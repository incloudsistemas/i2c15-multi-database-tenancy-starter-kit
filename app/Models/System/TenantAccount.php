<?php

namespace App\Models\System;

use App\Enums\DefaultStatusEnum;
use App\Enums\TenantAccountRoleEnum;
use App\Models\Polymorphics\Address;
use App\Observers\System\TenantAccountObserver;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TenantAccount extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'role',
        'name',
        'cpf_cnpj',
        'holder_name',
        'emails',
        'phones',
        'complement',
        'social_media',
        'opening_hours',
        'theme',
        'status',
        'settings',
        'custom',
    ];

    protected $casts = [
        'role'          => TenantAccountRoleEnum::class,
        'emails'        => 'array',
        'phones'        => 'array',
        'social_media'  => 'array',
        'opening_hours' => 'array',
        'theme'         => 'array',
        'status'        => DefaultStatusEnum::class,
        'settings'      => 'array',
        'custom'        => 'array',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            related: TenantCategory::class,
            table: 'tenant_account_tenant_category',
            foreignPivotKey: 'tenant_account_id',
            relatedPivotKey: 'category_id'
        );
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(related: TenantPlan::class, foreignKey: 'plan_id');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(related: Address::class, name: 'addressable');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(related: Tenant::class, foreignKey: 'tenant_id');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 150, 150)
            ->nonQueued();
    }

    /**
     * EVENT LISTENER.
     *
     */

    protected static function boot()
    {
        parent::boot();
        self::observe(TenantAccountObserver::class);
    }

    /**
     * SCOPES.
     *
     */

    public function scopeByRoles(Builder $query, array $roles): Builder
    {
        return $query->whereIn('role', $roles);
    }

    public function scopeByStatuses(Builder $query, array $statuses = [1]): Builder
    {
        return $query->whereIn('status', $statuses);
    }

    /**
     * MUTATORS.
     *
     */

    /**
     * CUSTOMS.
     *
     */

    public function getDisplayMainEmailAttribute(): ?string
    {
        return $this->emails[0]['email'] ?? null;
    }

    public function getDisplayAdditionalEmailsAttribute(): ?array
    {
        $additionalEmails = [];

        if (isset($this->emails[1]['email'])) {
            foreach (array_slice($this->emails, 1) as $email) {
                $additionalEmail = $email['email'];

                if (!empty($email['name'])) {
                    $additionalEmail .= " ({$email['name']})";
                }

                $additionalEmails[] = $additionalEmail;
            }
        }

        return !empty($additionalEmails) ? $additionalEmails : null;
    }

    public function getDisplayMainPhoneAttribute(): ?string
    {
        return $this->phones[0]['number'] ?? null;
    }

    public function getDisplayMainPhoneWithNameAttribute(): ?string
    {
        if (isset($this->phones[0]['number'])) {
            $mainPhone = $this->phones[0]['number'];
            $phoneName = $this->phones[0]['name'] ?? null;

            if (!empty($phoneName)) {
                $mainPhone .= " ({$phoneName})";
            }

            return $mainPhone;
        }

        return null;
    }

    public function getDisplayAdditionalPhonesAttribute(): ?array
    {
        $additionalPhones = [];

        if (isset($this->phones[1]['number'])) {
            foreach (array_slice($this->phones, 1) as $phone) {
                $additionalPhone = $phone['number'];

                if (!empty($phone['name'])) {
                    $additionalPhone .= " ({$phone['name']})";
                }

                $additionalPhones[] = $additionalPhone;
            }
        }

        return !empty($additionalPhones) ? $additionalPhones : null;
    }

    public function getFeaturedImageAttribute(): ?Media
    {
        $featuredImage = $this->getFirstMedia('avatar');

        if (!$featuredImage) {
            $featuredImage = $this->getFirstMedia('images');
        }

        return $featuredImage ?? null;
    }

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('attachments');
    }
}
