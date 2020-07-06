<template>
    <li v-bind:class="{ odd: message.direction==='inbound',unread: message.unread }" @mouseenter="showDeleteButton()" @mouseleave="hideDeleteButton()">
        <span class="user">{{message.name}}</span><a title="Archive Message" class="delete-message" @click="deleteMessage()"><i class="entypo-bag"></i></a>
        <p style="white-space: pre-line;word-break: break-all">
            {{message.text}}
            <img v-bind:src="message.mms" v-if="message.mms" style="width: 300px">
        </p>
        <span class="time">{{message.date}}</span>
    </li>
</template>
<script>
    export default {
        name: "chat-message",
        props: {message: {type: Object, required: true}},
        methods: {
            showDeleteButton: function () {
                $(this.$el).find('.delete-message').fadeIn();
            },
            hideDeleteButton: function () {
                $(this.$el).find('.delete-message').fadeOut();
            },
            deleteMessage: function () {
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
                                    type: 'message',
                                    message_type: inst.message.type,
                                    id: inst.message.id
                                }
                            }).then(response => {
                                inst.$parent.messages.splice(inst.$parent.messages.findIndex(msg => msg.id === inst.message.id), 1);
                                toastr.success(response.data.message);
                            }).catch(e => {
                                console.log(e);
                                toastr.error("Something went wrong");
                            });
                        }
                    }
                });
            },
        }
    }
</script>
<style scoped>
    .delete-message {
        color: #7f8186;
        zoom: 1;
        filter: alpha(opacity=100);
        opacity: 1;
        display: none;
    }

    .delete-message:hover {
        color: #ffffff;
        cursor: pointer;
    }
</style>