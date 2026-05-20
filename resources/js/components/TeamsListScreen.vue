<template>
    <section class="teams-screen">
        <h2>Tournament Teams</h2>
        <table class="teams-table">
            <thead>
                <tr><th>Team Name</th></tr>
            </thead>
            <tbody>
                <tr v-for="team in store.teams" :key="team.id">
                    <td>{{ team.name }}</td>
                </tr>
            </tbody>
        </table>
        <AppButton
            :disabled="store.loading || store.teams.length === 0"
            @click="store.generateFixtures()"
        >
            Generate Fixtures
        </AppButton>
    </section>
</template>

<script setup>
import { useLeagueStore } from '../stores/league.js';
import AppButton from './AppButton.vue';

const store = useLeagueStore();
</script>

<style scoped>
.teams-screen { max-width: 480px; }
h2 { font-size: 18px; margin: 0 0 12px; font-weight: 500; color: var(--ink-muted); }
.teams-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; background: var(--surface); border-radius: var(--radius); overflow: hidden; }
.teams-table th { text-align: left; padding: 12px 16px; background: var(--table-header-bg); color: var(--table-header-ink); font-weight: 600; }
.teams-table td { padding: 12px 16px; border-top: 1px solid var(--border); }
</style>
