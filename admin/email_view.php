<?php
session_start();
require_once 'dbconnection.php'; // Update this path as needed

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>Admin | Update Profile</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">

    <style>
        .info-box {
            color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .blue-bg {
            background: #3498db;
        }

        .header-bg {
            background: #36454F;
        }

        /* You should create additional classes for different colors as shown in the image */

        .count {
            display: block;
            font-size: 20px;
            font-weight: bold;
        }

        .title {
            font-size: 16px;
        }

        #wrapper {
            padding: 20px;
        }

        .info-box {
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 15px;
            color: #fff;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .orange-bg {
            background: #FFC300;
            box-shadow: 0 8px 15px rgba(52, 152, 219, 0.3);
        }

        .blue-bg {
            background: #3498db;
            box-shadow: 0 8px 15px rgba(52, 152, 219, 0.3);
        }

        .info-box .fa {
            font-size: 60px;
            margin-bottom: 10px;
        }

        .info-box .count {
            display: block;
            font-size: 30px;
            font-weight: 600;
        }

        .info-box .title {
            font-size: 18px;
            font-weight: 400;
        }

        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(52, 152, 219, 0.5);
        }

        /* Add more color styles for other info-boxes */

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .info-box {
                margin-bottom: 10px;
            }

            .info-box .fa {
                font-size: 40px;
            }

            .info-box .count {
                font-size: 24px;
            }

            .info-box .title {
                font-size: 16px;
            }
        }

        /* Vertical Button Group Styles */
        .list-group-item-action.active {
            background-color: #3498db;
            border-color: #3498db;
        }

        .list-group-item-action {
            border: none;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .list-group-item-action:hover {
            border-left: 3px solid #3498db;
            background-color: #f4f4f4;
        }


        /* Button Styles */
        .btn-primary.active,
        .btn-primary:active {
            background-color: #3498db;
            border-color: #3498db;
        }

        .btn-secondary.active,
        .btn-secondary:active {
            background-color: #95a5a6;
            border-color: #95a5a6;
        }

        .btn-block {
            margin-bottom: 10px;
        }

        #emailContent {
            background: #fff;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            /* Space from top content */
        }

        .list-group-item-action {
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            border-left: 4px solid transparent;
            /* Stylish border for active or hover state */
        }

        .list-group-item-action:hover,
        .list-group-item-action:focus {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }

        /* For unread messages, make them stand out */
        .list-group-item-action.unread {
            background-color: #e9f5ff;
            border-color: #3498db;
            font-weight: bold;
        }

        /* Style for the message header in each item */
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .message-header h5 {
            margin-bottom: 0;
            color: #333;
        }

        .message-header small {
            color: #6c757d;
        }

        /* Style for the email body snippet */
        .message-body {
            font-size: 0.9rem;
            color: #555;
        }

        /* Style for the email sender */
        .message-sender {
            color: #888;
            font-size: 1.8rem;
        }

        /* Added styles for responsiveness */
        @media (max-width: 768px) {
            .message-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .message-header h5 {
                font-size: 1rem;
            }

            .message-header small {
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>

    <section id="container">
        <header class="header header-bg">
            <div class="sidebar-toggle-box">
                <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
            </div>
            <a href="#" class="logo"><b>Admin Dashboard</b></a>
            <div class="nav notify-row" id="top_menu">


                </ul>
            </div>
            <div class="top-menu">
                <ul class="nav pull-right top-menu">
                    <li><a class="logout" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </header>

        <?php include ('sidebar.php'); ?>

        <br>


        <section id="main-content" style="margin-left: 210px;">
            <section class="wrapper" style="margin-top: 80px;">



                <div class="col-lg-2 col-md-3">
                    <button id="inboxBtn" class="btn btn-primary btn-block mb-2">Inbox</button>
                    <button id="sentBtn"></button>
                </div>

                <div class="col-lg-10 col-md-9">
                    <div id="emailContent">
                        <h2>Emails will be displayed here</h2>
                        <!-- Content will be updated by JavaScript -->
                    </div>
                </div>

                <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="emailModalLabel">Email Content</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Email content will be dynamically inserted here -->
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // When the inbox button is clicked, load inbox emails
                        document.getElementById('inboxBtn').addEventListener('click', function () {
                            loadInbox();
                        });

                        // Function to load inbox emails
                        function loadInbox() {
                            fetch('fetch_inbox.php') // Adjust the path as needed
                                .then(response => response.json())
                                .then(data => {
                                    let content = '<h2>Inbox Emails</h2>';
                                    data.forEach(email => {
                                        content += `
                        <div class="list-group-item list-group-item-action email-item ${email.is_read ? '' : 'unread'}" data-id="${email.id}" data-toggle="modal" data-target="#emailModal">
                            <div class="message-header">
                                <h5>${email.email}</h5>
                                <small>${email.submitted_at}</small> 
                            </div>
                            <p>${email.message}</p>
                        </div>
                    `;
                                    });
                                    document.getElementById('emailContent').innerHTML = content;
                                })
                                .catch(error => console.error('Error:', error));
                        }

                        loadInbox();


                        document.getElementById('emailContent').addEventListener('click', function (event) {
                            if (event.target.classList.contains('email-item') || event.target.closest('.email-item')) {
                                let emailItem = event.target.closest('.email-item');
                                let emailId = emailItem.getAttribute('data-id');


                                document.querySelector('#emailModal .modal-body').textContent = emailItem.querySelector('p').textContent;


                                fetch('update_email_status.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({ id: emailId })
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        console.log('Email status updated:', data);
                                        loadInbox();
                                    })
                                    .catch(error => console.error('Error updating email status:', error));
                            }
                        });
                    });
                </script>




            </section>
        </section>

    </section>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>




</body>

</html>