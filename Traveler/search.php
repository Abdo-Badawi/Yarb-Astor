<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Stays - HomeStays</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <?php include 'navTraveler.php'; ?>
    </header>
 
    <!-- Search Section -->
    <section class="search-section py-5">
        <div class="container">
            <div class="row">
                <!-- Search Filters -->
                <div class="col-lg-3">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Filters</h5>
                            
                            <!-- Location Filter -->
                            <div class="mb-4">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" placeholder="Enter location">
                            </div>

                            <!-- Date Range Filter -->
                            <div class="mb-4">
                                <label class="form-label">Check-in Date</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Check-out Date</label>
                                <input type="date" class="form-control">
                            </div>

                            <!-- Guests Filter -->
                            <div class="mb-4">
                                <label class="form-label">Number of Guests</label>
                                <select class="form-select">
                                    <option value="1">1 Guest</option>
                                    <option value="2">2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5+">5+ Guests</option>
                                </select>
                            </div>

                            <!-- Price Range Filter -->
                            <div class="mb-4">
                                <label class="form-label">Price Range</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" class="form-control me-2" placeholder="Min">
                                    <span>-</span>
                                    <input type="number" class="form-control ms-2" placeholder="Max">
                                </div>
                            </div>

                            <!-- Amenities Filter -->
                            <div class="mb-4">
                                <label class="form-label">Amenities</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="wifi">
                                    <label class="form-check-label" for="wifi">WiFi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pool">
                                    <label class="form-check-label" for="pool">Pool</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="kitchen">
                                    <label class="form-check-label" for="kitchen">Kitchen</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="parking">
                                    <label class="form-check-label" for="parking">Free Parking</label>
                                </div>
                            </div>

                            <!-- Property Type Filter -->
                            <div class="mb-4">
                                <label class="form-label">Property Type</label>
                                <select class="form-select">
                                    <option value="all">All Types</option>
                                    <option value="house">House</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="villa">Villa</option>
                                    <option value="cabin">Cabin</option>
                                </select>
                            </div>

                            <button class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </div>
                </div>

                <!-- Search Results -->
                <div class="col-lg-9">
                    <!-- Search Bar -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for stays...">
                                <button class="btn btn-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Results Count and Sort -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Found 24 stays</h5>
                        <select class="form-select" style="width: auto;">
                            <option>Sort by: Recommended</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Rating: High to Low</option>
                        </select>
                    </div>

                    <!-- Stay Cards -->
                    <div class="row">
                        <!-- Stay Card 1 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="../img/stay1.jpg" class="card-img-top" alt="Stay 1">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title">Cozy Mountain Retreat</h5>
                                        <div class="text-warning">
                                            <i class="fas fa-star"></i>
                                            <span>4.8</span>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted">Mountain View, California</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="h5 mb-0">$150</span>
                                            <small class="text-muted">/night</small>
                                        </div>
                                        <a href="details.html" class="btn btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stay Card 2 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="../img/stay2.jpg" class="card-img-top" alt="Stay 2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title">Beachside Paradise</h5>
                                        <div class="text-warning">
                                            <i class="fas fa-star"></i>
                                            <span>4.9</span>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted">Malibu, California</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="h5 mb-0">$200</span>
                                            <small class="text-muted">/night</small>
                                        </div>
                                        <a href="details.html" class="btn btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stay Card 3 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="../img/stay3.jpg" class="card-img-top" alt="Stay 3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title">Urban Loft</h5>
                                        <div class="text-warning">
                                            <i class="fas fa-star"></i>
                                            <span>4.7</span>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted">San Francisco, California</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="h5 mb-0">$180</span>
                                            <small class="text-muted">/night</small>
                                        </div>
                                        <a href="details.html" class="btn btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stay Card 4 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="../img/stay4.jpg" class="card-img-top" alt="Stay 4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title">Lakeside Cabin</h5>
                                        <div class="text-warning">
                                            <i class="fas fa-star"></i>
                                            <span>4.9</span>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted">Lake Tahoe, California</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="h5 mb-0">$220</span>
                                            <small class="text-muted">/night</small>
                                        </div>
                                        <a href="details.html" class="btn btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../Common/footer.php'; ?>


    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html> 