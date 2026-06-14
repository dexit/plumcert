<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'name', 'subject', 'body_html', 'description', 'from_name', 'active'])]
class EmailTemplate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /**
     * Variables that templates may reference, with human descriptions.
     *
     * @var array<string, string>
     */
    public const VARIABLES = [
        'customer_name'    => 'Customer full name',
        'contact_name'     => 'Primary contact name (business)',
        'company_name'     => 'Customer company / business name',
        'appliance'        => 'Appliance description (make + model)',
        'appliance_make'   => 'Appliance make',
        'appliance_model'  => 'Appliance model',
        'property_address' => 'Property address',
        'due_date'         => 'Date the service / certificate is due',
        'days_until'       => 'Days remaining until due',
        'last_service'     => 'Date of last service',
        'cert_type'        => 'Certificate type',
        'cert_number'      => 'Certificate number',
        'booking_url'      => 'Online booking link',
        'business'         => "Our company name (Plumcert)",
        'phone'            => 'Our contact phone number',
    ];

    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->where('active', true)->first();
    }

    /**
     * Render subject + body with {{ variable }} substitution.
     *
     * @param  array<string, string|int|null>  $vars
     * @return array{subject:string, body:string}
     */
    public function render(array $vars = []): array
    {
        return [
            'subject' => $this->substitute($this->subject, $vars),
            'body'    => $this->substitute($this->body_html, $vars),
        ];
    }

    private function substitute(string $content, array $vars): string
    {
        return preg_replace_callback('/\{\{\s*([a-z_]+)\s*\}\}/i', function ($m) use ($vars) {
            $key = $m[1];
            return (string) ($vars[$key] ?? '');
        }, $content) ?? $content;
    }
}
