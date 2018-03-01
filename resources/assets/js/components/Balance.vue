<template>
    <div class="card card-default">
        <div class="card-header">Balance</div>

        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center" v-for="balance in currencies">
                <div>
                    <strong>{{ balance.currency }}</strong>
                    <br />
                    <small>{{ balance.last_update }}</small>
                </div>
                <span class="badge badge-primary badge-pill">
                    {{ balance.amount }}
                </span>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                currencies: []
            }
        },
        mounted() {
            this.load()
        },
        methods: {
            load() {
                axios.get('/api/balance').then((r) => {

                    this.currencies = r.data.data;

                })
            }
        }
    }
</script>
