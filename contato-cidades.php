<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Contato - JC Pisos Intertravados</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

 <?php 

        // Nível (Caminho) - '' para a raiz ou index, '../' para subir um nivel, '../../' para subir dois niveis, e assim por diante.

        define('U', ''); 

        

        // ID da pagina no menu

        define('MENU', 0); 

        

        // Chama o Header

        include(U . 'includes/header-cidades.php');

    ?>

  
    <!-- Page Header Start -->
    <div class="container-fluid breadcrumb-novo">
      
           
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="" href="#">Home</a></li>
                    <li class="breadcrumb-item  active" aria-current="page">Contato</li>
                </ol>
            </nav>
    
    </div>
    <!-- Page Header End -->


    <!-- Contact Start -->
    <div class="container-fluid bg-light overflow-hidden px-lg-0 new-padding" >
        <div class="container contact px-lg-0">
            <div class="row g-0 mx-lg-0">
                <div class="col-lg-7 contact-text py-5 wow fadeIn" data-wow-delay="0.5s">
                    <div class="p-lg-5 ps-lg-0">
                        <h6 class="text-primary">Solicite seu orçamento, tire dúvidas e fale com nossa equipe técnica.</h6>
                        <h1 class="mb-4">Fale com a JC Pisos Intertravados</h1>
                          <form>
                            <div class="row g-3">
                                <div class="col-12 col-sm-12">
                                    <input type="text" class="form-control border-0" placeholder="Nome" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-12">
                                    <input type="email" class="form-control border-0" placeholder="Email" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-12">
                                    <input type="text" class="form-control border-0" placeholder="Telefone" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-12">
                                    <select class="form-select border-0" style="height: 55px;">
                                        <option selected>Tipo de Obra</option>
                                        <option value="1">Residencial/Condomínio</option>
                                        <option value="2">Comercial</option>
                                        <option value="3">Industrial</option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-12">
                                    <input type="text" class="form-control border-0" placeholder="Local da Obra" style="height: 55px;">
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control border-0" placeholder="Mensagem"></textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary  py-3 px-5" type="submit">Enviar Orçamento</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5 pe-lg-0" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <iframe class="position-absolute w-100 h-100" style="object-fit: cover;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3660.2707255041855!2d-47.47525780000001!3d-23.450697199999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c5f58afae571cb%3A0x8532fd1cbebb9355!2sJC%20PISOS%20INTERTRAVADOS!5e0!3m2!1sen!2sbr!4v1764182421303!5m2!1sen!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->


<?php   include(U . 'includes/footer.php');?>