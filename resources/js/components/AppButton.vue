<template>
    <button
        class="app-button"
        :class="variant"
        :disabled="disabled"
        @click="$emit('click', $event)"
    >
        <slot />
    </button>
</template>

<script setup>
defineProps({
    variant: { type: String, default: 'primary' },
    disabled: { type: Boolean, default: false },
});
defineEmits(['click']);
</script>

<style scoped>
.app-button {
    padding: 8px 16px;
    border: 0;
    border-radius: var(--radius);
    cursor: pointer;
    font: inherit;
    font-weight: 500;
    background: var(--accent);
    color: var(--table-header-ink);
    transition: background 120ms ease;
}
.app-button:hover:not(:disabled) { background: var(--accent-hover); }
.app-button:disabled { opacity: 0.5; cursor: not-allowed; }
.app-button.danger { background: var(--danger); }
.app-button.danger:hover:not(:disabled) { background: var(--danger-hover); }
</style>
