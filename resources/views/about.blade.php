@extends('layouts.app')
@section('content')
    <style>
        .content-wrapper {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 50px;
            /* Adjust this value as needed */
        }


        

        .team-member-card {
            width: 300px;
            height: 360px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            margin: 30px;
            display: inline-block;
            text-align: center;
            position: relative;
        }

        .team-member-img {
            width: 100%;
            border-radius: 10px;

        }



        .team-member-details {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .team-member-card {
            transition: transform 0.3s ease;
        }

        .team-member-card:hover {
            transform: translateY(-10px);
            border: 2px solid #b08968;
        }

        .team-member-details {
            font-size: 18px;
            margin-bottom: 0px;

        }

        .team-member-details p {
            font-size: 14px;
            margin-bottom: 0;
            line-height: 1.2;
        }

        h2{
            font-size: 45px;
        }
    </style>

    <div class="container text-center content-wrapper">
        <h4 class="mt-2">Exploring and customizing vibrant bead accessories.</h4>

        <div class="container">
            <p>
                Bilibeads is committed to providing a comprehensive platform where bead enthusiasts can
                explore,<br>purchase,
                and unleash their creativity through a wide range of bead-based products and craft ideas.
            </p>

            <h2>Why Bilibeads?</h2>
            <p>
                At Bilibeads, we believe in the power of self-expression and individuality. Our platform is designed<br>to
                cater to the diverse tastes and preferences of those who appreciate the artistry of bead accessories.
            </p>

            <h2>Join Us in the Bead Journey</h2>
            <p>
                Whether you're a seasoned bead enthusiast or just starting your journey, Bilibeads welcomes you<br>to
                explore
                the vibrant world of bead products and create accessories that reflect your unique style.
            </p>
        </div>

        <h2 class="text-center pt-5"><strong>Meet the Team</strong></h2>
        <section class="about">
            <div class="container">
                <div class="main">
                    <div class="team-member-card">
                        <img src="images/ken.jpg" alt="Team Member 1" class="team-member-img">
                        <div class="team-member-details">
                            <h5>Martheus Kenn Banaag</h5>
                            <p>Project Manager</p>
                        </div>
                    </div>
                    <div class="team-member-card">
                        <img src="images/me.jpg" alt="Team Member 2" class="team-member-img">
                        <div class="team-member-details">
                            <h5>Jorlan Prado</h5>
                            <p>Front-End Developer</p>
                        </div>
                    </div>
                    <div class="team-member-card">
                        <img src="images/gelo.jpg" alt="Team Member 2" class="team-member-img">
                        <div class="team-member-details">
                            <h5>Mark Gelo Rioflorido</h5>
                            <p>Front-End Developer</p>
                        </div>
                    </div>
                    <div class="team-member-card">
                        <img src="images/jean.jpg" alt="Team Member 2" class="team-member-img">
                        <div class="team-member-details">
                            <h5>Jean Collado</h5>
                            <p>Back-End Developer</p>
                        </div>
                    </div>
                    <div class="team-member-card">
                        <img src="images/james.jpg" alt="Team Member 2" class="team-member-img">
                        <div class="team-member-details">
                            <h5>James Andrei Ocampo</h5>
                            <p>Back-End Developer</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    

    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Allura&amp;display=swap" rel="stylesheet">

@endsection
