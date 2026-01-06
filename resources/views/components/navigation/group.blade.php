@props(['items' => []])

<div class="space-y-0.5">
    @foreach($items as $item)
    <x-navigation.item :href="$item['href']" :label="$item['label']" :icon="$item['icon'] ?? null"
        :active="$item['active'] ?? false" :badge="$item['badge'] ?? null" :indent="$item['indent'] ?? true" />
    @endforeach
</div>