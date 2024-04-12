<aside>
    <div id="sidebar" class="nav-collapse ">
        <ul class="sidebar-menu" id="nav-accordion">

            <p class="centered"><a href="#"><img src="assets/img/man.png" class="img-circle" width="60"></a></p>
            <h5 class="centered">
                <?php echo $_SESSION['login']; ?>
            </h5>

            
            <li class="mt">
                <a href="dashboard.php">
                    <i class="fa fa-dashboard "></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sub-menu">
                <a href="manage-users.php">
                    <i class="fa fa-users"></i>
                    <span>Users Management</span>
                </a>
            </li>

            <li class="sub-menu">
                <a href="post.php">
                    <i class="fa fa-bullhorn"></i>
                    <span>Post Management</span>
                </a>
            </li>

            <li class="sub-menu">
                <a href="email_view.php">
                    <i class="fa fa-envelope"></i>
                    <span>email</span>
                </a>
            </li> 

            <li class="sub-menu">
                <a href="change-password.php">
                    <i class="fa fa-file"></i>
                    <span>Change Password</span>
                </a>
            </li>
            
        </ul>
    </div>
</aside>
