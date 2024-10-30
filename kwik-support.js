$ = jQuery;
$(document).ready(function () {
    var number_of_posts = $('#kwik_support_number_of_posts').val();
    var number_of_posts_minus_one = number_of_posts - 1;
    var displayedMessages = $('.kwik_support_message_container').size();
    var viewCounter = 0;
    var currentView = 0;
    var nothingMore = false;
    var kwik_support_messages_length = $('.kwik_support_messages').length - 1;
    var messagesHeight = $('.kwik_support_messages').height();
    var setting_view_older_messages = $('#kwik_support_setting_view_older_messages').val();
    var setting_loading = $('#kwik_support_setting_loading').val();
    $('#kwik_support_messages_viewport').height(messagesHeight);
    if ($('#kwik_support_more').length) {
        $('#kwik_support_load_older_messages_button').addClass('button-disabled kwik_support_button-disabled');
    }
    $('#kwik_support_new_message_send').click(function () {
        var message_ID = $('#kwik_support_message_id').val();
		
		var message = $('#kwik_support_new_message_text_ifr').contents().find('body').html();
        //var message = $('#kwik_support_new_message_text').val();
        var comment_date = $('#kwik_support_comment_date').val();
        var is_reply = $(this).siblings('.kwik_support_is_reply').val();
        var current_user = $('#kwik_support_new_message_user').val();
        var time = $('#kwik_support_new_message_time').val();
        if (message === '') {
            return false;
        } else {
            $.ajax({
                type: 'POST',
                url: '../wp-content/plugins/kwik-support/message_post.php',
                data: ({
                    message_ID: message_ID,
                    message: message,
                    comment_date: comment_date,
                    is_reply: is_reply,
                    current_user: current_user,
                    time: time
                }),
                success: function () {
                    	$('#kwik_support_new_message').fadeOut(250).css({"text-align":"center"}).html("<h4>Your message has been added.</h4>").fadeIn(250).delay(1500).queue(function(){						
						window.location = window.location.href;						
						});
                }
            });
            return false;
        }
    });
    $('.kwik_support_new_reply_send').click(function () {
        var message = $(this).siblings('.kwik_support_new_reply_text').val();
        var message_ID = $(this).siblings('.kwik_support_message_id').val();
        var comment_date = $(this).siblings('.kwik_support_comment_date').val();
		var user_reply_email = $(this).siblings('.kwik_support_user_reply_email').val();
        var is_reply = $(this).siblings('.kwik_support_is_reply').val();
        var current_user = $(this).siblings('.kwik_support_new_reply_user').val();
        var time = $(this).siblings('.kwik_support_new_reply_time').val();
        if (message === '') {
            return false;
        } else {
            $.ajax({
                type: 'POST',
                url: '../wp-content/plugins/kwik-support/message_post.php',
                data: ({
                    message_ID: message_ID,
                    message: message,
                    comment_date: comment_date,
					user_reply_email: user_reply_email,
                    is_reply: is_reply,
                    current_user: current_user,
                    time: time
                }),
                success: function () {
                    
					$('#kwik_support_new_message').fadeOut(250).css({"text-align":"center"}).html("<h4>Your reply has been added.</h4>").fadeIn(250).delay(1500).queue(function(){						
						window.location = window.location.href;						
						});

                }
            });
            return false;
        }
    });
    $('.kwik_support_message_container').hover(function () {
        $(this).children('.kwik_support_reply_link').show();
    }, function () {
        $('.kwik_support_reply_link').hide();
    });
    $('.kwik_support_reply_link').toggle(function () {
        $(this).siblings('form').slideDown(250, function () {
            var currentHeight = $('#kwik_support_messages_viewport').outerHeight();
            var olderMessagesHeight = $('.kwik_support_messages').eq(currentView).outerHeight();
            $('#kwik_support_messages_viewport').animate({
                height: olderMessagesHeight
            }, 'slow');
            var currentScrollTop = $('#kwik_support_messages_viewport').scrollTop();
        })
    }, function () {
        $(this).siblings('form').slideUp(250, function () {
            var currentHeight = $('#kwik_support_messages_viewport').outerHeight();
            var olderMessagesHeight = $('.kwik_support_messages').eq(currentView).outerHeight();
            $('#kwik_support_messages_viewport').animate({
                height: olderMessagesHeight
            }, 'slow');
            var currentScrollTop = $('#kwik_support_messages_viewport').scrollTop();
        })
    });
    $('#kwik_support_load_newer_messages_button').addClass('button-disabled kwik_support_button-disabled');
    $('#kwik_support_load_newer_messages_button').click(function () {
        if (currentView === 0) {
            return false;
        } else {
            $('#kwik_support_load_older_messages_button').removeClass('button-disabled kwik_support_button-disabled');
            currentView--;
            var currentHeight = $('#kwik_support_messages_viewport').outerHeight();
            var olderMessagesHeight = $('.kwik_support_messages').eq(currentView).outerHeight();
            $('#kwik_support_messages_viewport').animate({
                height: olderMessagesHeight
            }, 'slow');
            var currentScrollTop = $('#kwik_support_messages_viewport').scrollTop();
            $('#kwik_support_messages_viewport').animate({
                scrollTop: currentScrollTop - olderMessagesHeight
            }, 'slow');
            $('#kwik_support_messages_viewport').animate({
                height: olderMessagesHeight
            }, 'slow');
            if (currentView === 0) {
                $('#kwik_support_load_newer_messages_button').addClass('button-disabled kwik_support_button-disabled');
            } else {
                $('#kwik_support_load_newer_messages_button').removeClass('button-disabled kwik_support_button-disabled');
            }
            return true;
        }
        return false;
    });
    $('#kwik_support_load_older_messages_button').click(function () {
        displayedMessages = $('.kwik_support_message_container').size();
        if (!$('#kwik_support_more').length) {
            $('#kwik_support_load_newer_messages_button').removeClass('button-disabled kwik_support_button-disabled');
            var lastMessageId = $('.kwik_support_message_container').eq(displayedMessages - 1).attr('id');
            var substrlastMessageId = lastMessageId.substr(10);
            if (kwik_support_messages_length === currentView && nothingMore === false) {
                viewCounter++;
                currentView++;
                kwik_support_messages_length = $('.kwik_support_messages').length;
                $('#kwik_support_load_older_messages_button').val(setting_loading);
                $.ajax({
                    type: "POST",
                    url: "../wp-content/plugins/kwik-support/more_messages.php",
                    cache: false,
                    data: ({
                        substrlastMessageId: substrlastMessageId,
                        number_of_posts: number_of_posts,
                        viewCounter: viewCounter
                    }),
                    success: function (html) {
                        $('#kwik_support_flash').hide();
                        $('#kwik_support_messages_wrapper').append(html);
                        var messagesHeight = $('#kwik_support_messages_wrapper').outerHeight();
                        var latestAppendedHeight = $('.kwik_support_messages').eq(viewCounter).outerHeight();
                        var previousMessages = messagesHeight - latestAppendedHeight;
                        $('#kwik_support_messages_viewport').animate({
                            height: latestAppendedHeight
                        }, 'slow');
                        $('#kwik_support_messages_viewport').animate({
                            scrollTop: previousMessages
                        }, 'slow');
                        $('#kwik_support_load_older_messages_button').val(setting_view_older_messages);
                        if ($('#kwik_support_more').length !== 0) {
                            nothingMore = true;
                            $('#kwik_support_load_older_messages_button').addClass('button-disabled kwik_support_button-disabled');
                        }
                    }
                });
            } else if (kwik_support_messages_length === currentView && nothingMore === true) {
                $('#kwik_support_load_older_messages_button').addClass('button-disabled kwik_support_button-disabled');
            } else {
                currentView++;
                var currentScrollTop = $('#kwik_support_messages_viewport').scrollTop();
                var currentHeight = $('#kwik_support_messages_viewport').outerHeight();
                var nextMessagesHeight = $('.kwik_support_messages').eq(currentView).outerHeight();
                $('#kwik_support_messages_viewport').animate({
                    height: nextMessagesHeight
                }, 'slow');
                $('#kwik_support_messages_viewport').animate({
                    scrollTop: currentHeight + currentScrollTop
                }, 'slow');
                if (currentView == kwik_support_messages_length && nothingMore === true) {
                    $('#kwik_support_load_older_messages_button').addClass('button-disabled kwik_support_button-disabled');
                }
            }
            return false;
        } else if (kwik_support_messages_length != currentView && nothingMore === true) {
            currentView++;
            currentScrollTop = $('#kwik_support_messages_viewport').scrollTop();
            currentHeight = $('#kwik_support_messages_viewport').outerHeight();
            nextMessagesHeight = $('.kwik_support_messages').eq(currentView).outerHeight();
            $('#kwik_support_messages_viewport').animate({
                height: nextMessagesHeight
            }, 'slow');
            $('#kwik_support_messages_viewport').animate({
                scrollTop: currentHeight + currentScrollTop
            }, 'slow');
            if (currentView == kwik_support_messages_length) {
                $('#kwik_support_load_older_messages_button').addClass('button-disabled kwik_support_button-disabled');
            }
            if (currentView === 0) {
                $('#kwik_support_load_newer_messages_button').addClass('button-disabled kwik_support_button-disabled');
            } else {
                $('#kwik_support_load_newer_messages_button').removeClass('button-disabled kwik_support_button-disabled');
            }
        } else {
            return false;
        }
        return false;
    });
});