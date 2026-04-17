<?php

namespace Botble\CarRentals\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Http\Requests\WhatsAppMessageTemplateRequest;
use Botble\CarRentals\Models\WhatsAppMessageTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class WhatsAppTemplateController extends BaseController
{
    public function index(): View
    {
        $this->pageTitle('WhatsApp Templates');

        $templates = WhatsAppMessageTemplate::query()
            ->orderBy('event_type')
            ->orderBy('name')
            ->paginate(20);

        return view('plugins/car-rentals::admin.whatsapp.templates.index', [
            'templates' => $templates,
        ]);
    }

    public function edit(WhatsAppMessageTemplate $template): View
    {
        $this->pageTitle('Edit WhatsApp Template');

        return view('plugins/car-rentals::admin.whatsapp.templates.edit', [
            'template' => $template,
            'placeholdersText' => implode(', ', $template->placeholders ?? []),
        ]);
    }

    public function update(WhatsAppMessageTemplateRequest $request, WhatsAppMessageTemplate $template): RedirectResponse
    {
        $validated = $request->validated();
        $templateContent = trim((string) ($validated['template_content'] ?? ''));
        $placeholders = $this->resolvePlaceholders((string) ($validated['placeholders'] ?? ''), $templateContent);

        $template->fill([
            'label' => trim((string) ($validated['label'] ?? '')),
            'description' => trim((string) ($validated['description'] ?? '')),
            'template_content' => $templateContent,
            'placeholders' => $placeholders,
            'is_active' => $request->boolean('is_active'),
        ]);

        $template->save();

        return redirect()
            ->route('car-rentals.whatsapp.templates.edit', $template)
            ->withSuccess('Template updated successfully.');
    }

    public function approve(WhatsAppMessageTemplate $template): RedirectResponse
    {
        $metadata = (array) ($template->metadata ?? []);
        $metadata['approval_status'] = 'approved';
        $metadata['approved_at'] = Carbon::now()->toDateTimeString();

        $template->forceFill([
            'is_active' => true,
            'metadata' => $metadata,
        ])->save();

        return redirect()
            ->route('car-rentals.whatsapp.templates.index')
            ->withSuccess("Template '{$template->name}' has been marked as locally approved. Meta approval must be done in WhatsApp Manager.");
    }

    protected function resolvePlaceholders(string $placeholderInput, string $templateContent): array
    {
        if (trim($placeholderInput) !== '') {
            return collect(explode(',', $placeholderInput))
                ->map(static fn (string $item): string => trim($item))
                ->filter()
                ->map(static fn (string $item): string => trim($item, "{} \t\n\r\0\x0B"))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $templateContent, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }
}
