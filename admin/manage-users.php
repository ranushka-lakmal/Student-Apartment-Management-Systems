<?php
session_start();
include 'dbconnection.php';



if (strlen($_SESSION['id'] == 0)) {
    header('location:logout.php');
} else {

    // for deleting user
    if (isset($_GET['id'])) {
        $adminid = $_GET['id'];
        $msg = mysqli_query($con, "delete from users where id='$adminid'");
        if ($msg) {
            echo "<script>alert('Data deleted');</script>";
        }
    }


    // For approving or rejecting users
    if (isset($_GET['action']) && isset($_GET['uid'])) {
        $uid = $_GET['uid'];
        $action = $_GET['action'];
        $newStatCode = 1; // Default to pending

        if ($action === 'approve') {
            $newStatCode = 0; // Approved
        } elseif ($action === 'reject') {
            $newStatCode = 9; // Rejected
        }

        // Update the user's stat_code in the database
        $updateQuery = mysqli_query($con, "UPDATE users SET stat_code='$newStatCode' WHERE id='$uid'");
        if ($updateQuery) {
            echo "<script>alert('User status updated successfully.');</script>";
        } else {
            echo "<script>alert('Failed to update user status.');</script>";
        }
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

        <title>Admin | Manage Users</title>
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
            <section id="main-content">
                <section class="wrapper">
                    <h3><i class="fa fa-angle-right"></i> Manage Users</h3>
                    <div class="row">



                        <div class="col-md-12">
                            <div class="content-panel">
                                <table class="table table-striped table-advance table-hover">
                                    <h4><i class="fa fa-angle-right"></i> All User Details </h4>
                                    <hr>
                                    <thead>
                                        <tr>
                                            <th>Sno.</th>
                                            <th class="hidden-phone">First Name</th>
                                            <th> Last Name</th>
                                            <th> Email Id</th>
                                            <th>User type</th>
                                            <th>Contact no.</th>
                                            <th>Reg. Date</th>
                                            <th>User Status</th> <!-- Added new header for User Status -->
                                            <th>Actions</th> <!-- Added new header for Actions -->
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <?php $ret = mysqli_query($con, "select * from users");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($ret)) {
                                            $statusClass = 'status-pending'; // Default class
                                            $statusText = 'Pending'; // Default text
                                            if ($row['stat_code'] == 0) {
                                                $statusClass = 'status-approved';
                                                $statusText = 'Approved';
                                            } elseif ($row['stat_code'] == 9) {
                                                $statusClass = 'status-rejected';
                                                $statusText = 'Rejected';
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $cnt; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['fname']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['lname']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['email']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['usr_typ']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['contactno']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['posting_date']; ?>
                                                </td>

                                                <td class="<?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </td>
                                                <td>

                                                    <a href="update-profile.php?uid=<?php echo $row['id']; ?>">
                                                        <button class="btn btn-primary btn-xs"><i
                                                                class="fa fa-pencil"></i></button>
                                                    </a>
                                                    <a href="manage-users.php?action=approve&uid=<?php echo $row['id']; ?>"
                                                        onclick="return confirm('Are you sure to approve the user?');">
                                                        <button class="btn btn-success btn-xs"><i
                                                                class="fa fa-check"></i></button>
                                                    </a>
                                                    <a href="manage-users.php?action=reject&uid=<?php echo $row['id']; ?>"
                                                        onclick="return confirm('Are you sure to reject the user?');">
                                                        <button class="btn btn-danger btn-xs"><i
                                                                class="fa fa-times"></i></button>
                                                    </a>
                                                    <a href="manage-users.php?id=<?php echo $row['id']; ?>"
                                                        onclick="return confirm('Do you really want to delete');">
                                                        <button class="btn btn-danger btn-xs"><i class="fa fa-trash-o "></i>
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $cnt = $cnt + 1;
                                        } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </section>
        <script src="assets/js/jquery.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
        <script src="assets/js/common-scripts.js"></script>
        <script>
            $(function () {
                $('select.styled').customSelect();
            });

        </script>

    </body>

    </html>
<?php } ?>