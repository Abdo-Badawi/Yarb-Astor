<?php
// filepath: c:\xampp\htdocs\SE\HomeStays-V1\Common\register.php

// Start the session
session_start();

// Include necessary files
include_once '../Controllers/AuthController.php';

// Initialize variables
$errMsg = null;
$successMsg = null;

// Check if there are success or error messages in the session
if (isset($_SESSION['registration_success'])) {
    $successMsg = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']); // Clear the message after displaying
}

if (isset($_SESSION['registration_error'])) {
    $errMsg = $_SESSION['registration_error'];
    unset($_SESSION['registration_error']); // Clear the message after displaying
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $userData = [
        'first_name' => $_POST['firstName'] ?? null,
        'last_name' => $_POST['lastName'] ?? null,
        'email' => $_POST['email'] ?? null,
        'password' => $_POST['password'] ?? null,
        'phone_number' => $_POST['phone'] ?? null,
        'gender' => $_POST['gender'] ?? null,
        'date_of_birth' => $_POST['birthday'] ?? null,
        'national_id' => $_POST['nationalId'] ?? null,
        'user_type' => $_POST['role'] ?? null
    ];

    // Validate form data
    if (in_array(null, $userData, true)) {
        $errMsg = "Please fill in all required fields.";
        $_SESSION['registration_error'] = $errMsg;
        header("Location: register.php");
        exit();
    }

    // Validate email format
    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
        $errMsg = "Invalid email format.";
        $_SESSION['registration_error'] = $errMsg;
        header("Location: register.php");
        exit();
    }

    // Validate password confirmation
    if ($_POST['password'] !== $_POST['confirmPassword']) {
        $errMsg = "Passwords do not match.";
        $_SESSION['registration_error'] = $errMsg;
        header("Location: register.php");
        exit();
    }

    // Handle file upload for profile picture
    $profilePicturePath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            $errMsg = "File size exceeds 2MB.";
            $_SESSION['registration_error'] = $errMsg;
            header("Location: register.php");
            exit();
        }
        $targetDir = "../uploads/profile_pictures/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
                $userData['profile_picture'] = $targetFilePath;
            } else {
                $errMsg = "Failed to upload profile picture.";
                $_SESSION['registration_error'] = $errMsg;
                header("Location: register.php");
                exit();
            }
        } else {
            $errMsg = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            $_SESSION['registration_error'] = $errMsg;
            header("Location: register.php");
            exit();
        }
    }

    // Add role-specific fields to userData
    if ($userData['user_type'] === 'host') {
        $userData['property_type'] = $_POST['propertyType'] ?? null;
        $userData['preferred_language'] = $_POST['preferredLanguageHost'] ?? null;
        $userData['bio'] = $_POST['bioHost'] ?? null;
        $userData['location'] = $_POST['locationHost'] ?? null;
    } elseif ($userData['user_type'] === 'traveler') {
        $userData['skills'] = $_POST['skills'] ?? null;
        $userData['language_spoken'] = $_POST['languageSpoken'] ?? null;
        $userData['preferred_language'] = $_POST['preferredLanguageTraveler'] ?? null;
        $userData['bio'] = $_POST['bioTraveler'] ?? null;
        $userData['location'] = $_POST['locationTraveler'] ?? null;
    }

    // Use AuthController to register the user
    $authController = new AuthController();
    
    // Check if email or national ID already exists using the controller
    if (!$authController->isEmailAvailable($userData['email'])) {
        $errMsg = "An account with this email already exists.";
        $_SESSION['registration_error'] = $errMsg;
        header("Location: register.php");
        exit();
    }
    
    if (!$authController->isNationalIdAvailable($userData['national_id'])) {
        $errMsg = "An account with this national ID already exists.";
        $_SESSION['registration_error'] = $errMsg;
        header("Location: register.php");
        exit();
    }
    
    // Register the user
    $result = $authController->register($userData);

    if ($result) {
        $_SESSION['registration_success'] = "Registration successful!";
        $_SESSION['user_role'] = $userData['user_type']; // Store the user's role in the session
        header("Location: registration-success.php");
        exit();
    } else {
        $errMsg = "Failed to register. Please try again.";
        $_SESSION['registration_error'] = $errMsg;
        error_log("Registration error: " . $errMsg);
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register - HomeStays</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="HomeStays, Cultural Exchange, Local Experience, Homestays" name="keywords">
    <meta content="Join HomeStays as a host or traveler to experience authentic cultural exchange" name="description">

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/main.css" rel="stylesheet">

    <style>
        .role-selector {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .role-selector:hover {
            transform: translateY(-5px);
        }
        .role-selector.selected {
            border: 2px solid var(--bs-primary) !important;
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }
        .photo-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--bs-primary);
        }
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>123 Street, New York, USA</small>
                    <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>+012 345 6789</small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>info@homestays.com</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href=""><i class="fab fa-twitter fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href=""><i class="fab fa-facebook-f fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href=""><i class="fab fa-linkedin-in fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href=""><i class="fab fa-instagram fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square" href=""><i class="fab fa-youtube fw-normal"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid sticky-top bg-white shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-2 py-lg-0">
                <a href="../index.html" class="navbar-brand">
                    <h1 class="m-0 text-uppercase text-primary" style="font-size: 1.5rem;"><i class="fa fa-home me-2"></i>HomeStays</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="../index.html" class="nav-item nav-link" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Home</a>
                        <a href="about.html" class="nav-item nav-link" style="font-size: 0.9rem; padding: 0.5rem 1rem;">About</a>
                        <a href="service.html" class="nav-item nav-link" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Services</a>
                        <a href="contact.html" class="nav-item nav-link" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Contact</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Register Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="wow fadeInUp" data-wow-delay="0.1s">
                        <div class="text-center">
                            <h6 class="section-title text-center text-primary text-uppercase">Join Our Community</h6>
                            <h1 class="mb-5">Register with <span class="text-primary text-uppercase">HomeStays</span></h1>
                        </div>

                        <?php if (!empty($errMsg)): ?>
                            <div class="alert alert-danger text-center">
                                <?php echo htmlspecialchars($errMsg); ?>
                            </div>
                        <?php endif; ?>

                        <form id="registrationForm" action="register.php" method="POST" enctype="multipart/form-data">
                            <!-- Role Selection -->
                            <div class="mb-4">
                                <h4 class="mb-3 text-center">Select Your Role</h4>
                                <div class="row justify-content-center">
                                    <!-- Host Role -->
                                    <div class="col-md-5">
                                        <div class="card role-selector text-center p-3" data-role="host">
                                            <div class="card-body">
                                                <i class="fa fa-home fa-3x text-primary mb-3"></i>
                                                <h5 class="card-title">Become a Host</h5>
                                                <p class="card-text">Share your home and culture with travelers from around the world.</p>
                                                <input type="radio" name="role" id="roleHost" value="host" class="d-none" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Traveler Role -->
                                    <div class="col-md-5">
                                        <div class="card role-selector text-center p-3" data-role="traveler">
                                            <div class="card-body">
                                                <i class="fa fa-plane fa-3x text-primary mb-3"></i>
                                                <h5 class="card-title">Join as Traveler</h5>
                                                <p class="card-text">Experience authentic local culture and make meaningful connections.</p>
                                                <input type="radio" name="role" id="roleTraveler" value="traveler" class="d-none" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information Section -->
                            <h4 class="mb-4">Basic Information</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" required>
                                        <label for="firstName">First Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name" required>
                                        <label for="lastName">Last Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                        <label for="email">Email</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                                        <label for="phone">Phone Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                        <label for="password">Password</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                                        <label for="confirmPassword">Confirm Password</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nationalId" name="nationalId" placeholder="National ID" required>
                                        <label for="nationalId">National ID</label>
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="" selected disabled>Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <label for="gender">Gender</label>
                                    </div>
                                </div>

                                <!-- Birthday -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="birthday" name="birthday" placeholder="Birthday" required>
                                        <label for="birthday">Birthday</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Basic Information End -->
                            <!-- National ID -->
                            

                            <!-- Profile Photo -->
                            <div class="col-12 mt-4">
                                <div class="text-center">
                                    <label for="photo" class="form-label">Profile Photo</label>
                                    <div class="mb-3">
                                        <img id="photoPreview" src="../img/default-avatar.jpg" class="photo-preview mb-2" alt="Profile Photo">
                                    </div>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
                                </div>
                            </div>

                            <!-- Role-Specific Fields -->
                            <div id="hostFields" class="form-section" style="display: none;">
                                <h4 class="mb-4">Host Information</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="propertyType" name="propertyType">
                                                <option value="" selected disabled>Select Property Type</option>
                                                <option value="teaching">Teaching</option>
                                                <option value="farming">Farming</option>
                                                <option value="cooking">Cooking</option>
                                                <option value="childcare">Childcare</option>
                                            </select>
                                            <label for="propertyType">Property Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="preferredLanguageHost" name="preferredLanguageHost" placeholder="Preferred Language">
                                            <label for="preferredLanguageHost">Preferred Language</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="bioHost" name="bioHost" placeholder="Bio" style="height: 100px;"></textarea>
                                            <label for="bioHost">Bio</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="locationHost" name="locationHost" placeholder="Location">
                                            <label for="locationHost">Location</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="travelerFields" class="form-section" style="display: none;">
                                <h4 class="mb-4">Traveler Information</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="skills" name="skills" placeholder="Skills (comma-separated)">
                                            <label for="skills">Skills</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="languageSpoken" name="languageSpoken" placeholder="Languages Spoken">
                                            <label for="languageSpoken">Languages Spoken</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="preferredLanguageTraveler" name="preferredLanguageTraveler" placeholder="Preferred Language">
                                            <label for="preferredLanguageTraveler">Preferred Language</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="bioTraveler" name="bioTraveler" placeholder="Bio" style="height: 100px;"></textarea>
                                            <label for="bioTraveler">Bio</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="locationTraveler" name="locationTraveler" placeholder="Location">
                                            <label for="locationTraveler">Location</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary py-3 px-5">Register</button>
                                </div>
                            </div>
                        </form>

                        <script>
                            // Show/Hide Role-Specific Fields
                            document.querySelectorAll('input[name="role"]').forEach(roleInput => {
                                roleInput.addEventListener('change', function () {
                                    const hostFields = document.getElementById('hostFields');
                                    const travelerFields = document.getElementById('travelerFields');

                                    if (this.value === 'host') {
                                        hostFields.style.display = 'block';
                                        travelerFields.style.display = 'none';
                                    } else if (this.value === 'traveler') {
                                        hostFields.style.display = 'none';
                                        travelerFields.style.display = 'block';
                                    }
                                });
                            });

                            // Ensure the correct fields are displayed on page load
                            const selectedRoleInput = document.querySelector('input[name="role"]:checked');
                            if (selectedRoleInput) {
                                selectedRoleInput.dispatchEvent(new Event('change'));
                            }

                            // Photo Preview
                            function previewPhoto(input) {
                                if (input.files && input.files[0]) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        document.getElementById('photoPreview').src = e.target.result;
                                    }
                                    reader.readAsDataURL(input.files[0]);
                                }
                            }

                            document.querySelector('input[value="host"]').click();
                            document.querySelector('input[value="traveler"]').click();

                            // Add click event to role-selector cards
                            document.querySelectorAll('.role-selector').forEach(card => {
                                card.addEventListener('click', function () {
                                    const role = this.getAttribute('data-role');
                                    const roleInput = document.querySelector(`input[name="role"][value="${role}"]`);
                                    if (roleInput) {
                                        roleInput.checked = true;
                                        roleInput.dispatchEvent(new Event('change'));
                                    }

                                    // Highlight the selected card
                                    document.querySelectorAll('.role-selector').forEach(c => c.classList.remove('selected'));
                                    this.classList.add('selected');
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Register End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-primary mb-4">Get In Touch</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>123 Street, New York, USA</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+012 345 6789</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@homestays.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-primary mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="about.html">About Us</a>
                    <a class="btn btn-link" href="contact.html">Contact Us</a>
                    <a class="btn btn-link" href="service.html">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">Support</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-primary mb-4">Newsletter</h4>
                    <p>Subscribe to our newsletter for the latest updates and offers.</p>
                    <div class="position-relative w-100">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">HomeStays</a>, All Right Reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>

    <script>
        // Role Selection
        let selectedRole = '';
        const roleSelectors = document.querySelectorAll('.role-selector');
        roleSelectors.forEach(selector => {
            selector.addEventListener('click', function() {
                roleSelectors.forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                selectedRole = this.dataset.role;
            });
        });

        // Form Navigation
        let currentSection = 0;
        const sections = ['basicInfo', 'personalInfo'];
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');

        nextBtn.addEventListener('click', () => {
            if (currentSection < sections.length - 1) {
                document.getElementById(sections[currentSection]).classList.remove('active');
                currentSection++;
                document.getElementById(sections[currentSection]).classList.add('active');
                
                if (currentSection === sections.length - 1) {
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'inline-block';
                }
                prevBtn.style.display = 'inline-block';
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentSection > 0) {
                document.getElementById(sections[currentSection]).classList.remove('active');
                currentSection--;
                document.getElementById(sections[currentSection]).classList.add('active');
                
                if (currentSection === 0) {
                    prevBtn.style.display = 'none';
                }
                nextBtn.style.display = 'inline-block';
                submitBtn.style.display = 'none';
            }
        });

        // Photo Preview
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form Submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission for validation

            // Clear previous error messages
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(msg => msg.remove());

            let isValid = true;

            // Validate Role Selection
            const selectedRole = document.querySelector('.role-selector.selected');
            if (!selectedRole) {
                isValid = false;
                showError('Please select a role (Host or Traveler).', document.querySelector('.role-selector').parentElement);
            }

            // Validate Password Confirmation
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                isValid = false;
                showError('Passwords do not match.', document.getElementById('confirmPassword').parentElement);
            }

            // Validate Email Format
            const email = document.getElementById('email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                isValid = false;
                showError('Please enter a valid email address.', document.getElementById('email').parentElement);
            }

            // Validate Required Fields
            const requiredFields = document.querySelectorAll('#registrationForm input[required], #registrationForm select[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    showError('This field is required.', field.parentElement);
                }
            });

            // If all validations pass, submit the form
            if (isValid) {
                console.log('Form is valid. Submitting...');
                return true; // Allow form submission
            }
        });

        // Function to Show Error Messages
        function showError(message, parentElement) {
            const error = document.createElement('div');
            error.className = 'error-message text-danger mt-1';
            error.textContent = message;
            parentElement.appendChild(error);
        }
    </script>
</body>
</html>


