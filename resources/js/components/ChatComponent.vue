<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <ul class="pl-0">
                            <li
                                    v-for="(message, index) in messages"
                                    :key="index"
                                    class="my-3"
                            >
                                <div
                                        v-if="message.user_id === userId"
                                        class="border rounded px-3 py-2 badge-light text-right"
                                >
                                    <div class="col-12 px-0">
                                        <small class="font-weight-bold">
                                            <span class="font-size-8">
                                                {{ message.created_at }}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="col-12 px-0">
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
                                            <span class="font-size-8">
                                                {{ message.created_at }}
                                            </span>
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
                                    maxlength="255"
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
                userId: null,
                interval: null
            }
        },
        methods: {
            listenForNewMessages: async function() {
                let response = await fetch("/getNew");
                if (response.status === 200) {
                    let resp = await response.json();
                    this.content = '';
                    this.messages.push(...resp.messages);
                    await this.listenForNewMessages();
                } else {
                    await this.listenForNewMessages();
                }
            },
            sendMessage: function() {
                if (this.content) {
                    Vue.axios.post('/storeMessage', {
                        'user_id': this.userId,
                        'content': this.content
                    });
                }
            }
        },
        created() {
            this.messages = this.messagesProp;
            this.userId = this.userIdProp;
            this.listenForNewMessages();
        },
    }
</script>

<style>
    ul {
        list-style: none;
    }
    .font-size-8 {
        font-size: 8px
    }
</style>
