<template>
    <div class="chat-conversation" style="bottom:0;display: block;opacity: 1;" v-bind:style="styleObject" @click="markAsRead()">
        <div class="conversation-header">
            <a href="#" class="conversation-close" @click="closeConversation()"><i class="entypo-cancel"></i></a>
            <a style="float: right;color: #7f8186;zoom: 1;filter: alpha(opacity=100);opacity: 1;position: relative;top: 3px;" @click="deleteConversation()" title="Archive Conversation"><i class="entypo-bag"></i></a>
            <a v-if="conversation.type === 'single'" href="#" style="float: right;color: #7f8186;zoom: 1;filter: alpha(opacity=100);opacity: 1;position: relative;top: 3px;" @click="editContact()" title="Edit Contact"><i class="entypo-newspaper"></i></a>
            <a v-if="conversation.type === 'single' && multiUser" href="#" style="float: right;color: #7f8186;zoom: 1;filter: alpha(opacity=100);opacity: 1;position: relative;top: 3px;" @click="createGroupConversation()" title="Create Group Conversation"><i class="entypo-user-add"></i></a>
            <a v-if="conversation.type === 'conversation' && multiUser" href="#" style="float: right;color: #7f8186;zoom: 1;filter: alpha(opacity=100);opacity: 1;position: relative;top: 3px;" @click="conversationUsers()" title="Edit Users"><i class="entypo-users"></i></a>
            <span class="user-status is-online"></span>
            <small></small>
            <img v-bind:src="conversation.avatar" class="img-circle" width="32">
            <span class="display-name" v-bind:title="title">{{conversation.name}}</span>
        </div>
        <ul class="conversation-body" v-if="isLoading" style="color: #06b53c;text-align: center;">
            <li>Please Wait...</li>
        </ul>
        <ul class="conversation-body" v-else>
            <chat-message v-for="message in sortedMessages" :message="message" :key="message.id"/>
        </ul>
        <div class="chat-textarea">
            <div v-if="newMessage.sending">
                <div class="lds-css ng-scope">
                    <div style="margin: auto" class="lds-facebook">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
            <form @submit.prevent="sendMessage" id="send-message" v-else>
                <textarea class="form-control autogrow" placeholder="Type your message" style="padding-right: 75px;resize: vertical;min-height: 32px;" v-model="newMessage.text" @keydown="inputHandler"></textarea>
                <i class="entypo-attach" id="attach_file" data-toggle="popover" data-trigger="hover" data-placement="top" data-html="true" @click="openFileDialog" data-content="Allowed file types: jpg,jpeg,png,gif<br>Max file size: 10 MB" data-original-title="Attach File">
                    <input type="file" name="mms" id="mms_file" style="display: none" @change="uploadFieldChange" accept="image/*">
                </i>
                <div class="btn-group dropup" v-if="messageTemplates.length > 0 && chatMessageTemplates" id="insert_template">
                    <i class="entypo-clipboard dropdown-toggle" title="Message Templates" data-toggle="dropdown"></i>
                    <ul class="dropdown-menu" role="menu">
                        <li v-for="template in messageTemplates">
                            <a href="#" @click="insertTemplate(template.text)">{{template.name}}</a>
                        </li>
                    </ul>
                </div>
                <i class="entypo-chat" title="Send" id="send_chat" @click="sendMessage"></i>
            </form>
        </div>
    </div>
</template>
<script>
    export default {
        name: "chat-conversation",
        props: {
            conversation: {
                type: Object,
                required: true,
            },
            index: {
                type: Number,
                required: true,
            },
        },
        data: function () {
            return {
                messages: [],
                isLoading: true,
                newMessage: {},
                messageTemplates: Config.messageTemplates,
                chatMessageTemplates: Config.chatMessageTemplates,
                resizable: Config.conversationResize,
                multiUser: Config.multipleChatUsers,
            };
        },
        methods: {
            closeConversation: function () {
                this.$emit('conversation-closed', this.conversation.id);
            },
            messageReceived: function (message, conversation) {
                if (conversation.id !== this.conversation.id) {
                    return;
                }
                let index;
                if (index = this.messages.findIndex(msg => msg.id === message.id) !== -1) {
                    this.messages[index] = message;
                } else {
                    this.messages.push(message);
                }
            },
            insertTemplate: function (text) {
                this.newMessage = {text: text};
            },
            openFileDialog: function () {
                $(this.$el).find('#mms_file').click();
            },
            uploadFieldChange: function (e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length) {
                    return;
                }
                this.newMessage.image = files[0];
                e.target.value = [];
                this.sendMessage();
            },
            sendMessage: function (e) {

                if (!this.newMessage.text && !this.newMessage.image) {
                    return;
                }

                let formData = new FormData();
                $.each(this.conversation.users, function (id, value) {
                    formData.append('receivers[' + id + '][id]', value.id);
                    formData.append('receivers[' + id + '][first_name]', value.first_name);
                    formData.append('receivers[' + id + '][last_name]', value.last_name);
                });
                if (this.newMessage.image) {
                    formData.append('mms', this.newMessage.image);
                }
                if (this.newMessage.text) {
                    formData.append('text', this.newMessage.text);
                }
                formData.append('type', this.conversation.type);
                formData.append('conversation_id', this.conversation.id);
                this.newMessage = {sending: true};
                window.axios.post('/messages', formData).then(response => {
                    let inst = this;
                    $.each(response.data, function (k, v) {
                        inst.messages.push(v);
                    });
                    this.newMessage = {};
                    this.$nextTick(function () {
                        $(this.$el).find("textarea.autogrow, textarea.autosize").autosize();
                    });
                }).catch(e => {
                    console.log(e);
                    toastr.error("Something went wrong");
                    this.newMessage.sending = false;
                });
            },
            inputHandler(e) {
                if (e.keyCode === 13 && !e.shiftKey) {
                    e.stopPropagation();
                    e.preventDefault();
                    e.returnValue = false;
                    this.sendMessage();
                } else if (e.keyCode === 27) {
                    this.closeConversation();
                }
            },
            markAsRead: function () {
                if (this.conversation.unreadMessagesCount > 0 && !this.isLoading) {
                    window.axios.post('/messages/mark-as-read', {params: this.conversation})
                          .then(response => {
                              this.conversation.unreadMessagesCount = 0;
                              this.messages.forEach(message => message.unread = false);
                          })
                          .catch(e => {
                              console.log(e);
                              this.markAsRead();
                          });
                }
            },
            loadMessages: function () {
                this.isLoading = true;
                window.axios.get('/messages', {params: this.conversation})
                      .then(response => {
                          this.messages = response.data;
                          this.isLoading = false;
                          this.conversation.unreadMessagesCount = 0;
                      })
                      .catch(e => {
                          console.log(e);
                          this.loadMessages();
                      });
            },
            deleteConversation: function () {
                let inst = this;
                BootstrapDialog.confirm({
                    title: 'Action Confirmation',
                    message: 'Do you really want to perform this action?',
                    type: BootstrapDialog.TYPE_DANGER,
                    closable: true,
                    draggable: false,
                    btnCancelLabel: 'Close',
                    btnOKLabel: 'Confirm',
                    btnOKClass: 'btn-danger',
                    callback: function (result) {
                        if (result) {
                            window.axios.delete('/messages/', {
                                params: {
                                    type: 'conversation',
                                    conversation_type: inst.conversation.type,
                                    id: inst.conversation.id
                                }
                            }).then(response => {
                                inst.$parent.conversations.splice(inst.$parent.conversations.findIndex(conversation => conversation.id === inst.conversation.id), 1);
                                inst.closeConversation();
                                toastr.success(response.data.message);
                            }).catch(e => {
                                console.log(e);
                                toastr.error("Something went wrong");
                            });
                        }
                    }
                });
            },
            editContact: function () {
                $.get(Config.defaultURL + "/messages/contact/" + this.conversation.users[0].id, function (data) {
                    let edit = $('#edit-contact');
                    edit.find('form')
                        .data('updated', false).attr('action', Config.defaultURL + '/messages/contact/' + data.phone)
                        .end()
                        .find('.alert')
                        .hide();
                    edit.find('input[type!=password][type!=hidden]').val("");
                    edit.find('textarea').val("").trigger("change");
                    edit.find('.avatar').attr('src', Config.defaultURL + '/assets/images/member.jpg');
                    $.each(data, function (k, v) {
                        edit.find('[name=' + k + '][type!=password][type!=file]').val(v).trigger("change");
                        if (jQuery.isArray(v)) {
                            edit.find('select[name=' + k + '\\[\\]]').val(v).trigger("change");
                        }
                        if (k === "avatar") {
                            if (v === null || !v.trim()) {
                                edit.find('.avatar').attr('src', Config.defaultURL + '/assets/images/member.jpg');
                            } else {
                                edit.find('.avatar').attr('src', v);
                            }
                        }
                    });
                    $.each(edit.find(".autogrow"), function (k, v) {
                        $(v).trigger('input');
                    });
                    edit.modal('show', {backdrop: 'static'});
                }, "json");
            },
            createGroupConversation: function () {
                window.axios.post('/messages/conversations', {
                    users: this.conversation.users,
                }).then(response => {
                    this.$parent.createConversation(response.data.conversation);
                    toastr.success(response.data.message);
                }).catch(e => {
                    console.log(e);
                    toastr.error("Something went wrong");
                });
            },
            conversationUsers: function () {
                let edit = $("#conversation_users");
                edit.find('form')
                    .data('updated', false)
                    .end()
                    .find('.alert')
                    .hide();
                edit.find('[name=conversation_id]').val(this.conversation.id);
                edit.find('[name=name]').val(this.conversation.name);
                edit.find('[name=users]').val(this.conversation.users.map(user => user.id)).trigger('change');
                edit.modal('show', {backdrop: 'static'});
            }
        },
        computed: {
            title: function () {
                let title = "";
                $.each(this.conversation.users, function (id, user) {
                    let name = user.id;
                    if (user.first_name || user.last_name) {
                        name = user.first_name + " " + user.last_name + "(" + user.id + ")";
                    }

                    title += name + "\n";
                });
                return title ? title : this.conversation.name;
            },
            sortedMessages: function () {
                return this.messages.sort(function (a, b) {
                    return a.sortOrder - b.sortOrder;
                });
            },
            styleObject: function () {
                return isxs() ? {} : {right: 280 + this.index * 350 + 'px'};
            }
        },
        watch: {
            messages: function (messages) {
                this.conversation.unreadMessagesCount = messages.filter(message => {
                    return message.unread;
                }).length;
                if (messages.length > 0) {
                    this.conversation.lastUpdate = this.sortedMessages[this.sortedMessages.length - 1].sortOrder;
                }
                this.$nextTick(function () {
                    let container = this.$el.querySelector(".conversation-body");
                    container.scrollTop = container.scrollHeight;
                })
            },
        },
        created: function () {
            this.loadMessages();
            this.$nextTick(function () {
                let inst = $(this.$el);
                inst.find('[data-toggle="popover"]').popover();
                inst.find("textarea.autogrow, textarea.autosize").autosize();

                if (this.resizable) {
                    inst.resizable({
                        handles: "n",
                        maxHeight: 750,
                        minHeight: 395,
                        resize: function (event, ui) {
                            inst.find(".conversation-body").css({height: ui.size.height - 145});
                        }
                    });
                }
            });
            vueChat.$on('message-received', this.messageReceived);
        },
    }
</script>
<style scoped>
    #attach_file {
        color: #bec0c2;
        right: 55px;
        top: 25px;
        font-size: 15px;
        position: absolute;
    }

    #attach_file:hover {
        color: #dee0e2;
        cursor: pointer;
    }

    #insert_template {
        color: #bec0c2;
        right: 75px;
        top: 25px;
        font-size: 15px;
        position: absolute;
    }

    #insert_template:hover {
        color: #dee0e2;
        cursor: pointer;
    }

    #send_chat {
        color: #bec0c2;
        right: 35px;
        top: 25px;
        font-size: 15px;
        position: absolute;
    }

    #send_chat:hover {
        color: #dee0e2;
        cursor: pointer;
    }

    @keyframes lds-facebook_1 {
        0% {
            top: 36px;
            height: 128px;
        }
        50% {
            top: 60px;
            height: 80px;
        }
        100% {
            top: 60px;
            height: 80px;
        }
    }

    @-webkit-keyframes lds-facebook_1 {
        0% {
            top: 36px;
            height: 128px;
        }
        50% {
            top: 60px;
            height: 80px;
        }
        100% {
            top: 60px;
            height: 80px;
        }
    }

    @keyframes lds-facebook_2 {
        0% {
            top: 41.99999999999999px;
            height: 116.00000000000001px;
        }
        50% {
            top: 60px;
            height: 80px;
        }
        100% {
            top: 60px;
            height: 80px;
        }
    }

    @-webkit-keyframes lds-facebook_2 {
        0% {
            top: 41.99999999999999px;
            height: 116.00000000000001px;
        }
        50% {
            top: 60px;
            height: 80px;
        }
        100% {
            top: 60px;
            height: 80px;
        }
    }

    @keyframes lds-facebook_3 {
        0% {
            top: 48px;
            height: 104px;
        }
        50% {
            top: 60px;
            height: 80px;
        }
        100% {
            top: 60px;
            height: 80px;
        }
    }

    @-webkit-keyframes lds-facebook_3 {
        0% {
            top: 48px;
            height: 104px;
        }
        50% {
            top: 60px;
            height: 80px;
        }
        100% {
            top: 60px;
            height: 80px;
        }
    }

    .lds-facebook {
        position: relative;
    }

    .lds-facebook div {
        position: absolute;
        width: 30px;
    }

    .lds-facebook div:nth-child(1) {
        left: 35px;
        background: #93dbe9;
        -webkit-animation: lds-facebook_1 1s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        animation: lds-facebook_1 1s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        -webkit-animation-delay: -0.2s;
        animation-delay: -0.2s;
    }

    .lds-facebook div:nth-child(2) {
        left: 85px;
        background: #689cc5;
        -webkit-animation: lds-facebook_2 1s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        animation: lds-facebook_2 1s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        -webkit-animation-delay: -0.1s;
        animation-delay: -0.1s;
    }

    .lds-facebook div:nth-child(3) {
        left: 135px;
        background: #5e6fa3;
        -webkit-animation: lds-facebook_3 1s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        animation: lds-facebook_3 1s cubic-bezier(0, 0.5, 0.5, 1) infinite;
    }

    .lds-facebook {
        width: 30px !important;
        height: 30px !important;
        -webkit-transform: translate(-15px, -15px) scale(0.15) translate(15px, 15px);
        transform: translate(-15px, -15px) scale(0.15) translate(15px, 15px);
    }
</style>