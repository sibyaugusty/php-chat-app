<?php require_once '../includes/header.php'; ?>
<div class="main-body">
    <section class="chat-app-main-body">
        <div class="chat-app-main-body__friends-list">
            <div class="user-profile">
                <div class="user-profile-header">
                    <div class="user-details">
                        <div class="avatar">
                            <img src="../<?php echo $_SESSION['profile_image'] ?>" alt="User Avatar">
                        </div>
                        <div class="user-expand-details">
                            <div class="user-name"><?php echo $_SESSION['user_name'] ?></div>

                        </div>
                    </div>
                    <div class="user-expand"><i id="user_shorthand_expand" class="fa-solid fa-chevron-down"></i></div>
                </div>
                <div class="user-profile-expanded">
                    <button id="logout" class="logout"><i class="fa-duotone fa-solid fa-right-from-bracket"></i>logout</button>
                </div>
            </div>
            <div class="search-area">
                <div class="search-box">
                    <div class="searchform">
                        <input id="s" type="text" value="Search Friends" />
                        <div class="close">
                            <span class="front"></span>
                            <span class="back"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="recent-chats">
                <!-- <?php for ($i = 0; $i < 20; $i++) { ?>
                    <div class="chat-item">
                        <div class="recent-chat-avatar">
                            <img src="../asset/profile/bis__profile_image.png" alt="recent chat Avatar">
                        </div>
                        <div class="chat-info">
                            <span class="name">John Doe</span>
                            <span class="status-dot online"></span>
                        </div>
                    </div>
                <?php
                        }
                ?> -->
            </div>
        </div>
        <div class="chat-app-main-body__chat-body">
            <div class="chat-body-header">
                <div class="current-user-chat-details">
                    <div class="current-chat-avatar">
                        <img src="../asset/profile/bis_profile_image.png" alt="current chat Avatar">
                    </div>
                    <span class="current-user-name">John Doe</span>
                </div>
            </div>
            <div class="chat-body-content">
                <div class="chat-history">
                    <!-- Receiver's Message -->
                    <div class="chat-message receiver">
                        <div class="message-content">
                            Hi there! How are you doing today?
                        </div>
                    </div>
                    <!-- Sender's Message -->
                    <div class="chat-message sender">
                        <div class="message-content">
                            I'm doing great, thanks! How about you?
                        </div>
                    </div>
                    <!-- Receiver's Message -->
                    <div class="chat-message receiver">
                        <div class="message-content">
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                            I'm good too. Just working on a new project.
                        </div>
                    </div>
                    <!-- Sender's Message -->
                    <div class="chat-message sender">
                        <div class="message-content">
                            That sounds awesome! Need any help?
                            That sounds awesome! Need any help?
                            That sounds awesome! Need any help?
                            That sounds awesome! Need any help?
                            That sounds awesome! Need any help?
                            That sounds awesome! Need any help?
                            That sounds awesome! Need any help?
                        </div>
                    </div>
                </div>
                <div class="chat-text">
                    <input type="text" class="chat-input" data-key="" id="messageInput" placeholder="Type your message..." />
                    <button class="send-button" id="sendButton"><i class="fa-regular fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
        <div class="chat-app-main-body__user-profile-details">

        </div>

    </section>
</div>
<?php require_once '../includes/footer.php' ?>

<script>

    var currentReceiverId;
    var pollingInterval;

    function startPolling() {
        if (currentReceiverId) {
            pollingInterval = setInterval(function() {
                loadChatHistory(currentReceiverId);
            }, 5000); 
        }
    }

    function loadChatHistory(receiverId) {
        var userId = <?php echo $_SESSION['user_id'] ?>; // Get the logged-in user's ID
        $.ajax({
            url: '../code/user_management.php',
            type: 'POST',
            data: {
                action: 'chatHistory',
                receiverId: receiverId
            },
            success: function(response) {
                var messages = JSON.parse(response);

                var chatHistoryContainer = $('.chat-history');
                chatHistoryContainer.empty();

                messages.forEach(function(message) {
                    var messageElement;

                    if (message.sender_id == userId) {
                        // Sender's message
                        messageElement = `
                        <div class="chat-message sender">
                            <div class="message-content">${message.message}</div>
                        </div>
                    `;
                    } else {
                        // Receiver's message
                        messageElement = `
                        <div class="chat-message receiver">
                            <div class="message-content">${message.message}</div>
                        </div>
                    `;
                    }

                    chatHistoryContainer.append(messageElement);
                });

                chatHistoryContainer.scrollTop(chatHistoryContainer[0].scrollHeight);
            }
        });
    }


    function fetchUsers() {
        $.ajax({
            type: 'POST',
            url: '../code/user_management.php',
            data: {
                action: 'fetchUsers'
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    renderUsers(data.users);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching users: ' + error);
            }
        });
    }

    function renderUsers(users) {
        var recentChats = $('.recent-chats');
        recentChats.empty();

        users.forEach(function(user) {
            var onlineStatus = user.online_status == 1 ? 'online' : 'offline';
            var statusDotClass = user.online_status == 1 ? 'status-dot online' : 'status-dot offline';

            var userHTML = `
                                <div class="chat-item" data-id="${user.user_id}" data-displayname="${user.user_name}" data-profileimage="${user.profile_image}">
                                    <div class="recent-chat-avatar">
                                        <img src="../${user.profile_image}" alt="User Avatar">
                                    </div>
                                    <div class="chat-info">
                                        <span class="name">${user.user_name}</span>
                                        <span class="${statusDotClass}"></span>
                                    </div>
                                </div>
                            `;
            recentChats.append(userHTML);
        });
    }

    $(document).ready(function() {
        $('#user_shorthand_expand').click(function() {
            if ($(this).hasClass('fa-chevron-down')) {
                $(this).removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $('.user-profile').addClass('expand');
            } else {
                $(this).removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $('.user-profile').removeClass('expand');
            }

        });

        fetchUsers();

        setInterval(fetchUsers, 8000);


        $('#logout').click(function() {
            $.ajax({
                type: 'POST',
                url: '../code/user_management.php',
                data: {
                    action: 'logout'
                },
                success: function(response) {
                    var response = JSON.parse(response);
                    if (response.success == true) {
                        window.location.href = '../pages/signin.php';
                    } else {
                        alert('Error logging out: ' + response.msg);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    alert('Error: ' + error);
                }
            });
        });
        $(document).on('click', '.chat-item', function() {
            var userId = $(this).data('id');
            var userName = $(this).data('displayname');
            var profileImage = $(this).data('profileimage');

            $('.current-user-name').text(userName);
            $('.current-chat-avatar img').attr('src', '../' + profileImage);

            $('#sendButton').attr('data-key', userId);

            currentReceiverId = userId;
            loadChatHistory(currentReceiverId);
            startPolling();
        });


        $('#sendButton').on('click', function() {
            var message = $('#messageInput').val();
            var receiverId = $(this).data('key');

            if (message.trim() !== '') {
                $.ajax({
                    url: '../code/user_management.php',
                    type: 'POST',
                    data: {
                        action: 'sendMessage',
                        message: message,
                        receiverId: receiverId
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            $('#messageInput').val('');

                            loadChatHistory(receiverId);
                        } else {
                            alert(result.msg);
                        }
                    },
                    error: function() {
                        alert('Error sending message');
                    }
                });
            } else {
                alert('Please type a message');
            }
        });


    });
</script>