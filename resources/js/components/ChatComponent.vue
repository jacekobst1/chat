<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Chat</div>

                    <div class="card-body">
                        <ul
                                v-for="message in messages"
                                :key="message.id"
                        >
                            <li>
                                <div
                                        v-if="message.user.id === userId"
                                        class="border rounded px-3 py-2 badge-light"
                                >
                                        <div class="col-12 px-0 text-right">
                                            {{ message.content }}
                                        </div>
                                </div>
                                <div
                                        v-else
                                        class="border rounded px-3 py-2 badge-secondary"
                                >
                                    <div class="col-12 px-0">
                                        <small class="font-weight-bold">
                                            {{ message.user.email }}
                                        </small>
                                    </div>
                                    <div class="col-12 px-0">
                                        {{ message.content }}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="card-footer text-muted">
                        <div class="input-group mb-3">
                            <input
                                    v-model="content"
                                    @keyup.enter="sendMessage"
                                    type="text"
                                    class="form-control"
                                    placeholder="Your message"
                            >
                            <div class="input-group-append">
                                <button
                                        @click="sendMessage"
                                        type="button"
                                        class="btn btn-primary"
                                >
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'chat-component',
        props: [
            'messagesProp',
            'userIdProp'
        ],
        data () {
            return {
                messages: [],
                content: '',
                userId: null
            }
        },
        methods: {
            getMessages: function() {
                console.log('a');
            },
            sendMessage: function() {
                this.content = '';
                console.log('b');
            }
        },
        created() {
            this.messages = this.messagesProp;
            this.userId = this.userIdProp;
        },
        mounted() {
            setInterval(this.getMessages, 1000);
        }
    }
</script>

<style>
    ul {
        list-style: none;
    }
</style>
