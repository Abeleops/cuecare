
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GG Billiards & Autocare | POS System</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <!-- Main Stylesheet -->
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body>
    <div id="landingPage">
        <!-- Landing Header -->
        <header id="landingHeader">
            <!-- Business Logo -->
            <div id="landingBrand">
                <img src="{{ asset('images/logo.png') }}" alt="GG Billiards & Autocare Logo" id="landingHeaderLogo">
                <span id="landingBrandName">GG BILLIARDS &amp; AUTOCARE</span>
            </div>
        </header>
        <!-- Fixed Navigation -->
        <nav id="landingNavigation" aria-label="Landing page navigation">
            <a href="#landingHome" class="landingNavLink active">Home</a>
            <a href="#landingAbout" class="landingNavLink">About</a>
            <a href="#landingPosSystem" class="landingNavLink">POS System</a>
            <a href="#landingTeam" class="landingNavLink">Our Team</a>
        </nav>
        <!-- Main Content -->
        <main id="landingMain">
            <!-- Home Section -->
            <section id="landingHome" class="landingSection" aria-labelledby="landingHeroTitle">
                <div id="landingHeroContent">
                    <!-- Main Logo -->
                    <img src="{{ asset('images/logo.png') }}" alt="GG Billiards & Autocare" id="landingHeroLogo">
                    <!-- Business Name -->
                    <div id="landingHeroBrand">
                        <h1 id="landingHeroTitle">GG BILLIARDS &amp; AUTOCARE</h1>
                        <div id="landingHeroSubtitle">
                            <span></span>
                            <p>Food &amp; Beverages</p>
                            <span></span>
                        </div>
                    </div>
                    <!-- POS System Badge -->
                    <div id="landingPosBadge">
                        <span>POINT-OF-SALE SYSTEM</span>
                    </div>
                    <!-- System Description -->
                    <p id="landingHeroDescription">
                        A web-based Point of Sale (POS) system for GG Billiards
                        &amp; Autocare Food &amp; Beverages that streamlines sales,
                        product management, customer records, and business reporting.
                    </p>
                </div>
            </section>
            <!-- About Section -->
            <section id="landingAbout" class="landingSection" aria-labelledby="landingAboutTitle">
                <div id="landingAboutContent">
                    <!-- About Image -->
                    <div id="landingAboutImageContainer">
                        <img src="{{ asset('images/AboutUsPhoto.png') }}" alt="GG Billiards & Autocare billiards area and food and beverage counter" id="landingAboutImage">
                    </div>
                    <!-- About Information -->
                    <div id="landingAboutInformation">
                        <div id="landingAboutHeading">
                            <h2 id="landingAboutTitle">About Us</h2>
                            <span id="landingAboutHeadingLine"></span>
                        </div>
                        <p id="landingAboutDescription">
                            GG Billiards &amp; Autocare is a leisure and service
                            establishment offering billiards entertainment,
                            autocare services, and food and beverages for customers.
                        </p>
                        <p id="landingAboutAddress">
                            1546 N. Zamora Street, Tondo, Manila
                        </p>
                        <p id="landingAboutProjectDescription">
                            This project was created as part of our capstone
                            for our client, GG Billiards &amp; Autocare.
                        </p>
                    </div>
                </div>
            </section>        
            <!-- POS System Section -->
            <section id="landingPosSystem" class="landingSection" aria-labelledby="landingPosSystemTitle">
                <div id="landingPosSystemWrapper">
                    <!-- Left Decorative Shapes -->
                    <div id="landingPosLeftDecorations" class="landingPosDecorations" aria-hidden="true">
                        <div class="landingPosDecorationBar"></div>
                        <div class="landingPosDecorationBar"></div>
                    </div>
                    <!-- Main POS Container -->
                    <div id="landingPosSystemContent">
                        <!-- Yellow Accent Line -->
                        <div id="landingPosTopLine" aria-hidden="true"></div>
                        <!-- Heading -->
                        <div id="landingPosHeading">
                            <h2 id="landingPosSystemTitle">Food &amp; Beverages POS System</h2>
                            <p>A web-based POS for managing sales, products, customers, and reports.</p>
                        </div>
                        <!-- POS Laptop Image -->
                        <div id="landingPosImageContainer">
                            <img
                                src="{{ asset('images/posLaptop.png') }}"
                                alt="Preview of the GG Billiards and Autocare Point-of-Sale System displayed on a laptop"
                                id="landingPosLaptopImage"
                            >
                        </div>
                        <!-- Access POS Button -->
                        <a href="{{ route('login') }}" id="landingPosLoginButton">
                            <span class="material-symbols-outlined" aria-hidden="true">login</span>
                            <span>Access POS Login</span>
                        </a>
                        <!-- POS System Features -->
                        <div id="landingPosFeatures">
                            <div class="landingPosFeature">
                                <span class="material-symbols-outlined" aria-hidden="true">bolt</span>
                                <h3>Faster Transactions</h3>
                            </div>
                            <div class="landingPosFeature">
                                <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                                <h3>Inventory Management</h3>
                            </div>
                            <div class="landingPosFeature">
                                <span class="material-symbols-outlined" aria-hidden="true">monitoring</span>
                                <h3>Sales Reports</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Right Decorative Shapes -->
                    <div id="landingPosRightDecorations" class="landingPosDecorations" aria-hidden="true">
                        <div class="landingPosDecorationBar"></div>
                        <div class="landingPosDecorationBar"></div>
                    </div>
                </div>
            </section>       
            <!-- Our Team Section -->
            <section id="landingTeam" class="landingSection" aria-labelledby="landingTeamTitle">
                <div id="landingTeamContent">
                    <!-- Team Header -->
                    <div id="landingTeamHeader">
                        <div id="landingTeamLogoContainer">
                            <img src="{{ asset('images/bullsecLogo.png') }}" alt="BullSec Team Logo" id="landingTeamLogo">
                        </div>
                        <h2 id="landingTeamTitle">Our Team</h2>
                        <span id="landingTeamHeadingLine" aria-hidden="true"></span>
                        <h3 id="landingTeamBrandName">BullSec</h3>
                    </div>            
                    <!-- Team Members -->
                    <div id="landingTeamMembers">
                        <!-- Group Leader -->
                        <article class="landingTeamCard">
                            <div class="landingTeamPhotoContainer">
                                <img src="{{ asset('images/ivan.png') }}" alt="Ivan Mark Lizardo" class="landingTeamMemberPhoto">
                            </div>
                            <div class="landingTeamMemberInformation">
                                <h3>Lizardo</h3>
                                <p class="landingTeamFirstName">Ivan Mark</p>
                                <span class="landingTeamMemberLine" aria-hidden="true"></span>
                                <p class="landingTeamRole">Group Leader</p>
                            </div>
                        </article>
                        <!-- Backend Developer -->
                        <article class="landingTeamCard">
                            <div class="landingTeamPhotoContainer">
                                <img src="{{ asset('images/abele.png') }}" alt="Abele John Juarez" class="landingTeamMemberPhoto">
                            </div>
                            <div class="landingTeamMemberInformation">
                                <h3>Juarez</h3>
                                <p class="landingTeamFirstName">Abele John</p>
                                <span class="landingTeamMemberLine" aria-hidden="true"></span>
                                <p class="landingTeamRole">Backend Developer</p>
                            </div>
                        </article>
                        <!-- Database Developer -->
                        <article class="landingTeamCard">
                            <div class="landingTeamPhotoContainer">
                                <img src="{{ asset('images/ken.png') }}" alt="Ken Anthonie Bautista" class="landingTeamMemberPhoto">
                            </div>
                            <div class="landingTeamMemberInformation">
                                <h3>Bautista</h3>
                                <p class="landingTeamFirstName">Ken Anthonie</p>
                                <span class="landingTeamMemberLine" aria-hidden="true"></span>
                                <p class="landingTeamRole">Database Developer</p>
                            </div>
                        </article>
                        <!-- UI/UX Designer -->
                        <article class="landingTeamCard">
                            <div class="landingTeamPhotoContainer">
                                <img src="{{ asset('images/nico.png') }}" alt="Nico Evangelo Llaguno" class="landingTeamMemberPhoto">
                            </div>
                            <div class="landingTeamMemberInformation">
                                <h3>Llaguno</h3>
                                <p class="landingTeamFirstName">Nico Evangelo</p>
                                <span class="landingTeamMemberLine" aria-hidden="true"></span>
                                <p class="landingTeamRole">UI/UX Design</p>
                            </div>
                        </article>
                    </div>

                </div>
            </section>

        </main>
    </div>
</body>
</html>
