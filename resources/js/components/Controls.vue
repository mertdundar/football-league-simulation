<template>
    <div class="controls">
        <AppButton
            :disabled="!store.canPlayAll"
            @click="store.playAll()"
        >
            Play All Weeks
        </AppButton>
        <AppButton
            :disabled="!store.canPlayNext"
            @click="store.playNext()"
        >
            Play Next Week
        </AppButton>
        <AppButton
            variant="danger"
            :disabled="store.loading"
            @click="store.reset()"
        >
            Reset Data
        </AppButton>
    </div>
</template>

<script setup>
import AppButton from './AppButton.vue';
import { useLeagueStore } from '../stores/league.js';

const store = useLeagueStore();
</script>

<style scoped>
.controls {
    display: flex;
    justify-content: space-around;
    gap: 16px;
    margin-top: 24px;
    flex-wrap: wrap;
}
</style>
