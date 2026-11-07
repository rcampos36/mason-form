<?php

/**
 * Approved presentation choices.
 *
 * ACF (or any other field) may store one of the keys below. The returned
 * value is always a complete class string from this file. Unknown keys fall
 * back to the group default and are never interpolated into a class name.
 */

namespace App;

class Design
{
    /**
     * @var array<string, string>
     */
    private const FALLBACKS = [
        'alert' => 'default',
        'align' => 'left',
        'button' => 'primary',
        'card' => 'default',
        'field' => 'control',
        'announcement' => 'default',
        'footer' => 'default',
        'footerInner' => 'default',
        'header' => 'default',
        'headerBar' => 'static',
        'heading' => '2',
        'image' => 'frame',
        'layout' => 'contained',
        'link' => 'inline',
        'nav' => 'desktop',
        'tag' => 'default',
        'tone' => 'default',
        'type' => 'body',
        'width' => 'page',
        'actions' => 'left',
        'aspect' => 'video',
        'columns' => '3',
        'dialog' => 'default',
        'divider' => 'none',
        'gallery' => '3',
        'height' => 'standard',
        'hero' => 'shell',
        'intro' => 'stacked',
        'portfolio' => 'grid',
        'position' => 'right',
        'space' => 'medium',
        'stack' => 'left',
    ];

    /**
     * @return array<string, array<string, string>>
     */
    private static function maps(): array
    {
        return [
            'alert' => [
                'default' => 'border border-border bg-surface px-6 py-4 text-small text-foreground',
                'success' => 'border border-border bg-surface px-6 py-4 text-small text-foreground',
                'caution' => 'border border-border bg-surface px-6 py-4 text-small text-muted',
                'warning' => 'border border-border border-l-2 border-l-accent bg-surface px-6 py-4 text-small text-foreground',
            ],
            'announcement' => [
                'default' => 'border-b border-border bg-surface px-6 py-3 text-center text-small text-foreground',
            ],
            'actions' => [
                'left' => 'flex flex-wrap gap-4',
                'center' => 'flex flex-wrap justify-center gap-4',
            ],
            'align' => [
                'left' => 'text-left',
                'center' => 'text-center',
                'right' => 'text-right',
            ],
            'aspect' => [
                'video' => 'aspect-video',
                'square' => 'aspect-square',
                'wide' => 'aspect-[21/9]',
                'portrait' => 'aspect-[4/5]',
            ],
            'columns' => [
                '2' => 'grid gap-8 sm:grid-cols-2',
                '3' => 'grid gap-8 sm:grid-cols-2 lg:grid-cols-3',
                '4' => 'grid gap-8 sm:grid-cols-2 lg:grid-cols-4',
            ],
            'dialog' => [
                'default' => 'w-[min(72rem,calc(100vw-2rem))] max-w-none border-0 bg-background p-6 text-foreground backdrop:bg-background/80',
            ],
            'divider' => [
                'none' => '',
                'line' => 'border-t border-border',
            ],
            'gallery' => [
                '2' => 'grid gap-4 sm:grid-cols-2',
                '3' => 'grid gap-4 sm:grid-cols-2 lg:grid-cols-3',
                '4' => 'grid gap-4 sm:grid-cols-2 lg:grid-cols-4',
            ],
            'height' => [
                'compact' => 'min-h-[80vh]',
                'standard' => 'min-h-screen',
                'tall' => 'min-h-screen',
                'screen' => 'min-h-screen',
            ],
            'hero' => [
                'shell' => 'relative isolate flex overflow-hidden',
                'content' => 'relative mx-auto flex w-full max-w-wide flex-col justify-end gap-8 px-5 py-section md:px-8 lg:px-gutter',
                'image' => 'absolute inset-0 -z-10 h-full w-full object-cover',
                'imageFallback' => 'absolute inset-0 -z-10 hidden h-full w-full object-cover motion-reduce:block',
                'video' => 'absolute inset-0 -z-10 h-full w-full object-cover motion-reduce:hidden',
                'shade' => 'absolute inset-0 -z-10 bg-gradient-to-t from-background via-background/45 to-transparent',
            ],
            'intro' => [
                'stacked' => 'grid items-start gap-10',
                'image-left' => 'grid items-start gap-10 md:grid-cols-2 lg:gap-16',
                'image-right' => 'grid items-start gap-10 md:grid-cols-2 lg:gap-16',
            ],
            'button' => [
                'primary' => 'inline-flex items-center justify-center bg-accent px-6 py-4 text-button uppercase text-on-accent transition-colors duration-300 ease-out hover:bg-accent-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent',
                'secondary' => 'inline-flex items-center justify-center border border-border bg-transparent px-6 py-4 text-button uppercase text-foreground transition-colors duration-300 ease-out hover:border-foreground focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent',
                'ghost' => 'inline-flex items-center justify-center bg-transparent px-6 py-3 text-button uppercase text-muted transition-colors duration-300 ease-out hover:text-foreground focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent',
            ],
            'card' => [
                'default' => 'grid gap-6 border-t border-border bg-transparent pt-8',
            ],
            'footer' => [
                'default' => 'mt-auto',
                'stacked' => 'mt-auto bg-surface',
            ],
            'footerInner' => [
                'default' => 'mx-auto grid w-full max-w-wide gap-12 border-t border-border px-5 py-16 text-small text-muted md:grid-cols-2 md:px-8 lg:grid-cols-12 lg:gap-10 lg:px-gutter lg:py-20',
                'stacked' => 'mx-auto grid w-full max-w-wide gap-12 border-t border-border px-5 py-16 text-small text-muted md:px-8 lg:px-gutter lg:py-20',
            ],
            'field' => [
                'label' => 'grid gap-2 text-small text-muted',
                'labelText' => 'text-nav uppercase text-muted',
                'control' => 'w-full border border-border bg-surface px-4 py-3 text-body text-foreground transition-colors duration-300 ease-out placeholder:text-muted focus:border-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent motion-reduce:transition-none',
            ],
            'header' => [
                'default' => 'bg-background',
                'minimal' => 'bg-background',
            ],
            'headerBar' => [
                'static' => 'relative z-40',
                'sticky' => 'sticky top-0 z-50 bg-background',
            ],
            'heading' => [
                'display' => 'font-sans text-display font-extrabold text-balance text-foreground',
                '1' => 'font-sans text-h1 font-extrabold text-balance text-foreground',
                '2' => 'font-sans text-h2 font-bold text-balance text-foreground',
                '3' => 'font-sans text-h3 font-semibold text-foreground',
                '4' => 'font-sans text-h4 font-medium text-foreground',
                '5' => 'font-sans text-h5 font-medium text-foreground',
                '6' => 'font-sans text-h6 font-medium text-foreground',
            ],
            'image' => [
                'frame' => 'overflow-hidden bg-surface',
                'media' => 'aspect-[3/2] h-full w-full object-cover',
                'landscape' => 'aspect-[3/2] h-full w-full object-cover',
                'content' => 'h-full w-full object-cover',
                'logo' => 'h-8 w-auto max-w-48 object-contain',
                'cover' => 'absolute inset-0 -z-10 h-full w-full object-cover',
                'icon' => 'h-12 w-12 object-cover',
                'avatar' => 'h-16 w-16 object-cover',
                'mark' => 'h-12 w-auto max-w-40 object-contain',
                'portrait' => 'aspect-[4/5] h-full w-full object-cover',
            ],
            'layout' => [
                'contained' => 'mx-auto w-full max-w-page px-5 py-section md:px-8 lg:px-gutter',
                'compact' => 'mx-auto w-full max-w-page px-5 py-section-sm md:px-8 lg:px-gutter',
                'wide' => 'mx-auto w-full max-w-wide px-5 py-section md:px-8 lg:px-gutter',
                'full' => 'w-full px-5 py-section md:px-8 lg:px-gutter',
                'stack' => 'mx-auto grid w-full max-w-page gap-10 px-5 py-section md:px-8 lg:px-gutter',
                'split' => 'mx-auto grid w-full max-w-page items-start gap-10 px-5 py-section md:grid-cols-2 md:px-8 lg:gap-16 lg:px-gutter',
                'thirds' => 'mx-auto grid w-full max-w-wide gap-8 px-5 py-section sm:grid-cols-2 md:px-8 lg:grid-cols-3 lg:px-gutter',
                'images' => 'mx-auto grid w-full max-w-wide gap-4 px-5 py-section sm:grid-cols-2 md:px-8 lg:px-gutter',
                'bleed' => 'w-full',
                'logos' => 'grid grid-cols-2 items-center gap-10 sm:grid-cols-3 lg:grid-cols-4',
                'steps' => 'grid list-none border-t border-border',
                'step' => 'grid gap-6 border-b border-border py-8 md:grid-cols-[6rem_minmax(0,1fr)_16rem] md:items-start',
                'stats' => 'grid gap-10 sm:grid-cols-2 lg:grid-cols-4',
            ],
            'link' => [
                'inline' => 'text-foreground underline decoration-border underline-offset-4 transition-colors duration-300 ease-out hover:text-accent hover:decoration-accent',
                'accent' => 'text-accent underline decoration-accent underline-offset-4 transition-colors duration-300 ease-out hover:text-foreground',
                'nav' => 'flex h-full items-center whitespace-nowrap border-l border-border px-6 text-nav uppercase text-current transition-colors duration-300 ease-out hover:text-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent motion-reduce:transition-none',
                'navCurrent' => 'flex h-full items-center whitespace-nowrap border-l border-border px-6 text-nav uppercase text-current underline decoration-current underline-offset-8 transition-colors duration-300 ease-out hover:text-accent hover:decoration-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent motion-reduce:transition-none',
                'navMobile' => 'text-h2 font-medium text-current transition-colors duration-300 ease-out hover:text-inverse-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-8 focus-visible:outline-inverse-accent motion-reduce:transition-none',
                'navMobileCurrent' => 'text-h2 font-medium text-current underline decoration-current underline-offset-[0.18em] transition-colors duration-300 ease-out hover:text-inverse-accent hover:decoration-inverse-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-8 focus-visible:outline-inverse-accent motion-reduce:transition-none',
                'footer' => 'text-small text-foreground transition-colors duration-300 ease-out hover:text-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent motion-reduce:transition-none',
                'footerCurrent' => 'text-small text-foreground underline decoration-foreground underline-offset-4 transition-colors duration-300 ease-out hover:text-accent hover:decoration-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent motion-reduce:transition-none',
                'brand' => 'text-nav font-medium uppercase text-current transition-colors duration-300 ease-out hover:text-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent motion-reduce:transition-none',
                'filter' => 'text-nav uppercase text-muted transition-colors duration-300 ease-out hover:text-foreground focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent motion-reduce:transition-none',
                'filterCurrent' => 'text-nav uppercase text-foreground underline decoration-foreground underline-offset-8 transition-colors duration-300 ease-out hover:text-accent hover:decoration-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent motion-reduce:transition-none',
                'card' => 'group grid text-foreground no-underline outline-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent',
            ],
            'nav' => [
                'desktop' => 'flex h-full list-none items-stretch',
                'mobile' => 'grid list-none gap-10',
                'footer' => 'grid list-none gap-3',
            ],
            'portfolio' => [
                'grid' => 'grid gap-x-8 gap-y-16 border-t border-border pt-10 sm:grid-cols-2',
                'list' => 'grid border-t border-border',
            ],
            'position' => [
                'left' => 'md:order-first',
                'right' => 'md:order-last',
                'image-left' => 'md:order-first',
                'image-right' => 'md:order-last',
            ],
            'space' => [
                'none' => 'py-0',
                'small' => 'py-section-sm',
                'medium' => 'py-section',
                'large' => 'py-32 md:py-40',
            ],
            'stack' => [
                'left' => 'grid max-w-content gap-6',
                'center' => 'mx-auto grid max-w-content gap-6',
            ],
            'tag' => [
                'default' => 'inline-flex items-center text-caption uppercase text-muted',
                'accent' => 'inline-flex items-center bg-accent px-2 py-1 text-caption uppercase text-background',
                'caption' => 'mt-3 text-caption text-muted',
            ],
            'tone' => [
                'default' => 'bg-background text-foreground',
                'surface' => 'bg-surface text-foreground',
                'inverse' => 'bg-inverse text-inverse-foreground',
            ],
            'type' => [
                'display' => 'font-sans text-display font-extrabold text-balance text-foreground',
                '1' => 'font-sans text-h1 font-extrabold text-balance text-foreground',
                '2' => 'font-sans text-h2 font-bold text-balance text-foreground',
                '3' => 'font-sans text-h3 font-semibold text-foreground',
                '4' => 'font-sans text-h4 font-medium text-foreground',
                '5' => 'font-sans text-h5 font-medium text-foreground',
                '6' => 'font-sans text-h6 font-medium text-foreground',
                'lead' => 'text-lead',
                'body' => 'text-body',
                'small' => 'text-small text-muted',
                'caption' => 'text-caption uppercase tracking-wide text-muted',
            ],
            'width' => [
                'content' => 'mx-auto w-full max-w-content',
                'page' => 'mx-auto w-full max-w-page',
                'wide' => 'mx-auto w-full max-w-wide',
                'full' => 'w-full',
            ],
        ];
    }

    public static function classes(string $group, ?string $choice = null): string
    {
        $map = self::maps()[$group] ?? null;

        if ($map === null) {
            return '';
        }

        if ($choice !== null && array_key_exists($choice, $map)) {
            return $map[$choice];
        }

        $fallback = self::FALLBACKS[$group] ?? array_key_first($map);

        return $map[$fallback] ?? '';
    }
}
