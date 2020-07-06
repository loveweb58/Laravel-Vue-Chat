<template>
    <div>
        <div class="chat-inner">
            <h2 class="chat-header">
                <a @click="close()" class="chat-close"><i class="entypo-cancel"></i></a>
                <i class="entypo-users"></i>
                Chat
                <span class="badge badge-success" v-if="totalUnreadMessagesCount > 0">{{totalUnreadMessagesCount}}</span>
            </h2>
            <div class="chat-group" id="group-1" v-bind:style="{maxHeight: (fullHeight - 140) + 'px'}">
                <strong>Chat with:
                    <input id="new_conversation" name="new_conversation" style="font-size:10px;width:85px" title="" data-mask="19999999999">
                    <button type="button" class="btn btn-green btn-xs" @click="createVConversation()">
                        New
                    </button>
                </strong>
                <div v-if="isLoading" style="color: #06b53c;text-align: center;margin-top: 30px">
                    Please Wait...
                </div>
                <a v-for="conversation in sortedConversations" href="#" @click="openConversation(conversation.id)" style="display: block;" v-else>
                    <span class="user-status is-online"></span><em>{{conversation.name}}</em><span v-if="conversation.unreadMessagesCount > 0" class="badge badge-success">{{conversation.unreadMessagesCount}}</span>
                </a>
                <div v-if="ajaxLoading" style="color: #06b53c;text-align: center;margin-top: 30px">
                    Please Wait...
                </div>
            </div>
        </div>
        <chat-conversation v-for="(conversation, index) in activeConversations" :conversation="conversation" :index=index :key="conversation.id" @conversation-closed="closeConversation"/>
    </div>
</template>
<script>
    export default {
        name: "chat-widget",
        data: function () {

            return {
                conversations: [],
                activeConversationsLimit: Config.activeConversationsLimit,
                activeConversations: [],
                minimizedConversations: [],
                isLoading: true,
                ajaxLoading: false,
                visible: false,
                fullHeight: document.documentElement.clientHeight,
                currentPage: 1,
            }
        },
        computed: {
            totalUnreadMessagesCount: function () {
                let total = this.conversations.reduce((total, conversation) => total + conversation.unreadMessagesCount, 0);
                clearTimeout(titleTimer);
                document.title = Config.defaultTitle;
                if (total > 0) {
                    animateTitle("...You got " + total + " new messages", Config.defaultTitle, 1000);
                } else {
                    document.title = Config.defaultTitle;
                }
                $("#chat-total-unread-messages").html(total).toggleClass('is-hidden', total === 0);
                return total;
            },
            sortedConversations: function () {
                return this.conversations.sort(function (a, b) {
                    return b.lastUpdate - a.lastUpdate;
                });
            }
        },
        methods: {
            openConversation: function (id, autoOpen = true) {
                if (!this.visible && autoOpen) {
                    this.open();
                }
                let mcIndex = -1;
                if (this.activeConversations.findIndex(conversation => conversation.id === id) !== -1) {
                    return;
                }

                else if ((mcIndex = this.minimizedConversations.findIndex(conversation => conversation.id === id)) !== -1) {
                    this.activeConversations.push(this.minimizedConversations[mcIndex]);
                    this.minimizedConversations.splice(mcIndex, 1);
                }
                else {
                    this.activeConversations.push(this.conversations.find(conversation => conversation.id === id));
                }
                if (this.activeConversations.length > this.activeConversationsLimit) {
                    this.minimizedConversations.push(this.activeConversations.shift());
                }
            },
            closeConversation: function (id) {
                this.activeConversations.splice(this.activeConversations.findIndex(conversation => conversation.id === id), 1);
                if (this.activeConversations.length < this.activeConversationsLimit && this.minimizedConversations.length > 0) {
                    this.activeConversations.push(this.minimizedConversations.shift());
                }
            },
            createConversation: function (conversation, autoOpen = true) {
                if (this.conversations.findIndex(con => con.id === conversation.id) === -1) {
                    this.conversations.push(conversation);
                }
                if (autoOpen) {
                    this.openConversation(conversation.id);
                }
            },
            createVConversation: function () {
                let receiver = $(this.$el).find('#new_conversation').val();
                receiver = receiver.replace(/[^0-9]/g, '');
                if (receiver.length !== 11 || receiver.substring(0, 1) !== "1") {
                    return false;
                }
                this.createConversation({
                    id: sha1(receiver),
                    name: receiver,
                    users: [
                        {
                            first_name: null,
                            last_name: null,
                            id: receiver
                        },
                    ],
                    unreadMessagesCount: 0,
                    avatar: '/assets/images/member.jpg',
                    type: 'single',
                    lastUpdate: (new Date().getTime()) / 1000
                });
                $(this.$el).find('#new_conversation').val("");
            },
            updateConversation: function (id, newData) {
                console.log(newData);
                console.log(this.conversations);
                let conv = this.conversations.find(conversation => conversation.id === id);
                conv.name = newData.name;
                conv.users = newData.users;
            },
            open: function () {
                this.visible = true;
                if (isxs()) {
                    $(".page-container").addClass('chat-visible toggle-click');
                } else {
                    $(".page-container").addClass("chat-visible");
                }
            },
            close: function () {
                this.visible = false;
                $(".page-container").removeClass("chat-visible toggle-click");
                this.activeConversations = [];
            },
            toggle: function () {
                $(".page-container").hasClass("chat-visible") ? this.close() : this.open();
            },
        }, created: function () {
            window.axios.get('/messages/conversations')
                  .then(response => {
                      this.conversations = response.data;
                      this.isLoading = false;
                  })
                  .catch(e => {
                      console.log(e);
                  });
        }, mounted: function () {
            const vm = this;
            $(function () {
                $(vm.$el).find('.chat-inner').niceScroll({
                    cursorcolor: '#454a54',
                    cursorborder: '1px solid #454a54',
                    railpadding: {right: 3},
                    railalign: 'right',
                    cursorborderradius: 1
                }).scrollend(function () {
                    if (vm.ajaxLoading) {
                        return;
                    }
                    vm.ajaxLoading = true;
                    vm.currentPage += 1;
                    window.axios.get('/messages/conversations', {params: {page: vm.currentPage}})
                          .then(response => {
                              vm.conversations = vm.conversations.concat(response.data);
                              vm.ajaxLoading = false;
                          })
                          .catch(e => {
                              console.log(e);
                              vm.ajaxLoading = false;
                          });
                });
            });
            Echo.private('messages.' + Config.user.id).listen('MessageWasReceived', function (e) {
                vm.createConversation(e.data['conversation'], false);
                vm.conversations.find(conversation => conversation.id === e.data['conversation'].id).unreadMessagesCount += 1;
                vueChat.$emit('message-received', e.data['vue_message'], e.data['conversation']);
                notifyBrowser(e.data['chat_user'], e.message.mms ? 'You received new MMS' : e.message.text, e.message.sender, e.data['avatar'], e.data['conversation']);
            });
        }
    }
</script>
<style scoped>

</style>