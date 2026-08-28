@extends('dashboard-layout.index')

@section('content')

<div class="right_col" role="main" style="min-height: 1323px;"> 
<div class="x_panel">
<div class="x_title">
<h2>Create New Page</h2>
</div>
<form id="frmPageEdit" class="form-horizontal form-label-left" method="post" action="" data-parsley-validate="" novalidate="">
<div class="x_content">
<div class="row mb-3">
<label class="col-form-label col-sm-3 col-md-3 col-lg-3">Page Name</label>
<div class="col-sm-9 col-md-7 col-lg-6">
<input type="text" id="pageName" name="pageName" value="" class="form-control mb-2" required="">
<div class="form-text">Page url:
<a href="https://crmdemo.goride.run/" target="_blank" id="liveUrl">https://crmdemo.goride.run/</a> </div>
</div>
</div>
<div class="row mb-3">
<label class="col-form-label col-sm-3 col-md-3 col-lg-3">Title</label>
<div class="col-sm-9 col-md-7 col-lg-6"><input type="text" id="pageTitle" name="pageTitle" value="" class="form-control" required=""></div>
</div>
<div class="row mb-3">
<label class="col-form-label col-sm-3 col-md-3 col-lg-3">Description</label>
<div class="col-sm-9 col-md-7 col-lg-6"><input type="text" id="pageDescription" name="pageDescription" value="" class="form-control"></div>
</div>
<div class="row mb-3">
<label class="col-form-label col-sm-3 col-md-3 col-lg-3">Keywords</label>
<div class="col-sm-9 col-md-7 col-lg-6"><input type="text" id="pageKeywords" name="pageKeywords" value="" class="form-control">
</div></div>
<div class="row mb-3">
<label class="col-form-label col-sm-3 col-md-3 col-lg-3">OG Banner</label>
<div class="col-sm-9 col-md-7 col-lg-6">
<div class="input-group">
<input type="text" id="pageOGBanner" name="pageOGBanner" value="" class="form-control">
<button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#myModal"><i class="fa-solid fa-image"></i> Select Banner</button>
</div>
</div>
</div>
<div class="row mb-3">
<label class="col-form-label col-sm-3 col-md-3 col-lg-3">Head Tags</label>
<div class="col-sm-9 col-md-7 col-lg-6"><textarea id="pageHeadTags" name="pageHeadTags" class="form-control" style="height: 50px;"></textarea></div>
</div>
<div class="row mb-3">
<label class="col-form-label col-sm-3 col-md-3 col-lg-3">Custom JS &amp; CSS</label>
<div class="col-sm-9 col-md-7 col-lg-6"><textarea id="pageCustomJSCSS" name="pageCustomJSCSS" class="form-control" style="height: 50px;"></textarea></div>
</div>
<div class="row mb-3">TEMPLATES: 
<div class="col-12 col-sm-auto mb-3 mb-sm-0">
<div class="align-items-center border p-2 bg-light d-flex gap-3 rounded text-center">
<div class="my-0 h4">1</div>
<div class="hstack gap-2">
<button type="button" class="btn btn-outline-secondary templatePreview" id="1"><i class="fa-solid fa-eye"></i></button>
<button type="button" class="btn btn-outline-primary templateInsert" id="t1"><i class="fa-solid fa-circle-plus"></i></button>
</div>
</div>
</div><div class="col-12 col-sm-auto mb-3 mb-sm-0">
<div class="align-items-center border p-2 bg-light d-flex gap-3 rounded text-center">
<div class="my-0 h4">2</div>
<div class="hstack gap-2">
<button type="button" class="btn btn-outline-secondary templatePreview" id="2"><i class="fa-solid fa-eye"></i></button>
<button type="button" class="btn btn-outline-primary templateInsert" id="t2"><i class="fa-solid fa-circle-plus"></i></button>
</div>
</div>
</div><div class="col-12 col-sm-auto mb-3 mb-sm-0">
<div class="align-items-center border p-2 bg-light d-flex gap-3 rounded text-center">
<div class="my-0 h4">3</div>
<div class="hstack gap-2">
<button type="button" class="btn btn-outline-secondary templatePreview" id="3"><i class="fa-solid fa-eye"></i></button>
<button type="button" class="btn btn-outline-primary templateInsert" id="t3"><i class="fa-solid fa-circle-plus"></i></button>
</div>
</div>
</div><div class="col-12 col-sm-auto mb-3 mb-sm-0">
<div class="align-items-center border p-2 bg-light d-flex gap-3 rounded text-center">
<div class="my-0 h4">4</div>
<div class="hstack gap-2">
<button type="button" class="btn btn-outline-secondary templatePreview" id="4"><i class="fa-solid fa-eye"></i></button>
<button type="button" class="btn btn-outline-primary templateInsert" id="t4"><i class="fa-solid fa-circle-plus"></i></button>
</div>
</div>
</div><div class="col-12 col-sm-auto mb-3 mb-sm-0">
<div class="align-items-center border p-2 bg-light d-flex gap-3 rounded text-center">
<div class="my-0 h4">5</div>
<div class="hstack gap-2">
<button type="button" class="btn btn-outline-secondary templatePreview" id="5"><i class="fa-solid fa-eye"></i></button>
<button type="button" class="btn btn-outline-primary templateInsert" id="t5"><i class="fa-solid fa-circle-plus"></i></button>

</div>


</div>
</div>
</div>
<div id="summernote">
    
</div>
<!--//modal-->
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true"
aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">
          Select image
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="imgPreview">
          <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/28667239-7FE9-47C7-B69F-9C48A1914D83.jpeg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/3BBA5243-3195-4549-ACE1-5AB2274C0292.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/7dabf0c30ea74901a901993d09627859.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/Blue Korea Travel and Tours Agency Logo.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/a-Mercedes-S-class-300x136.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/a-Mercedes-mpv300x136.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/a-Vito-Car-7-300x136.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/a-minibus.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/airport-transportation.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/align-right-2x.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/app-download.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/app-store-badge.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/car-drive.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/driver-beside-car.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/estate.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/executive.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/google-play-badge.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/group-transportation.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/heads1.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/heads5.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home1-hero.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home1_slide1-edited.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home2-img1.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home2-img2.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home2-img3.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home2-img4.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home2-img5.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home2-img6.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home3-hero.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home3-img1.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home3-img2.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home3-img3.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home3-img4.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home3-img5.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home3-img6.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-hero.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-hero1.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-hero3.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img1.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img2.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img3.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img4.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img5.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img6.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img7.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img8.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/home4-img9.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/hotel-transfers.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/img-faq.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/img-fea-1.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/img-fea-2.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/img-fea-3.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/img-taxi.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/mpv.png"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/pexels-cottonbro-studio-4606397-min.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/pexels-ketut-subiyanto-4436356.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/saloon.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/secbg.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/sss.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/station-transfers.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
              <div class="border rounded bg-white" style="width:100%;height:150px;overflow:hidden;">
                <img class="img-fluid ogBannerImage" src="https://demo.cabookie.com/assets/images/uploads/wedding-packages.jpg"
                alt="" style="margin:0 auto" loading="lazy">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--///end modal-->
</div>
</form>
</div>
</div>

@endsection

@section('custom_scripts')
    @include('page.partials.employees_js')
@endsection
