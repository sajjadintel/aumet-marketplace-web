<?php
ob_start("compress_htmlcode");
function compress_htmlcode($codedata)
{
    $searchdata = array(
        '/\>[^\S ]+/s', // remove whitespaces after tags
        '/[^\S ]+\</s', // remove whitespaces before tags
        '/(\s)+/s' // remove multiple whitespace sequences
    );
    $replacedata = array('>', '<', '\\1');
    $codedata = preg_replace($searchdata, $replacedata, $codedata);
    return $codedata;
}
?>
<div class="d-flex flex-column-fluid">

    <div class="container">

        <div class="container">

            <div class="row">
                <div class="col-xl-6">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 p-8 card-rounded bgi-no-repeat d-flex align-items-center" style="background-color: #FFF4DE; background-position: left bottom; background-size: auto 100%; background-image: url(/theme/assets/media/svg/humans/custom-2.svg)">
                                <div class="row">
                                    <div class="col-12 col-xl-5"></div>
                                    <div class="col-12 col-xl-7">
                                        <h4 class="text-danger font-weight-bolder">Join SAP now to get 35% off</h4>
                                        <p class="text-dark-50 my-5 font-size-xl font-weight-bold">Offering discounts for your online store can be a powerful weapon in to drive loyalty</p>
                                        <a href="#" class="btn btn-danger font-weight-bold py-2 px-6">Join SaaS</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-3">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 bg-danger p-8 card-rounded flex-grow-1 bgi-no-repeat" style="background-position: calc(100% + 0.5rem) bottom; background-size: auto 70%; background-image: url(/theme/assets/media/svg/humans/custom-3.svg)">
                                <h4 class="text-inverse-danger mt-2 font-weight-bolder">User Confidence</h4>
                                <p class="text-inverse-danger my-6">Boost marketing &amp; sales
                                    <br>through product confidence.</p>
                                <a href="#" class="btn btn-warning font-weight-bold py-2 px-6">Learn</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-3">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0 card-rounded">
                            <div class="flex-grow-1 p-10 card-rounded flex-grow-1 bgi-no-repeat" style="background-color: #663259; background-position: calc(100% + 0.5rem) bottom; background-size: auto 75%; background-image: url(/theme/assets/media/svg/humans/custom-4.svg)">
                                <h4 class="text-inverse-danger mt-2 font-weight-bolder">Based On</h4>
                                <div class="mt-5">
                                    <div class="d-flex mb-5">
                                        <span class="svg-icon svg-icon-md svg-icon-white flex-shrink-0 mr-3">

                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                                </g>
                                            </svg>

                                        </span>
                                        <span class="text-white">Activities</span>
                                    </div>
                                    <div class="d-flex mb-5">
                                        <span class="svg-icon svg-icon-md svg-icon-white flex-shrink-0 mr-3">

                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                                </g>
                                            </svg>

                                        </span>
                                        <span class="text-white">Sales</span>
                                    </div>
                                    <div class="d-flex">
                                        <span class="svg-icon svg-icon-md svg-icon-white flex-shrink-0 mr-3">

                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                                </g>
                                            </svg>

                                        </span>
                                        <span class="text-white">Releases</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-xl-4">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 bg-light-success p-12 pb-40 card-rounded flex-grow-1 bgi-no-repeat" style="background-position: calc(100% + 0.5rem) bottom; background-size: 35% auto; background-image: url(/theme/assets/media/svg/humans/custom-5.svg)">
                                <p class="text-success pt-10 pb-5 font-size-h3 font-weight-bolder line-height-lg">Start with a branding
                                    <br>site design modern
                                    <br>content creation</p>
                                <a href="#" class="btn btn-success font-weight-bold py-2 px-6">Join Now</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-4">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 bg-info p-12 pb-40 card-rounded flex-grow-1 bgi-no-repeat" style="background-position: right bottom; background-size: 55% auto; background-image: url(/theme/assets/media/svg/humans/custom-6.svg)">
                                <h3 class="text-inverse-info pb-5 font-weight-bolder">Start Now</h3>
                                <p class="text-inverse-info pb-5 font-size-h6">Offering discounts for better
                                    <br>online a store can loyalty
                                    <br>weapon into driving</p>
                                <a href="#" class="btn btn-success font-weight-bold py-2 px-6">Join Now</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-4">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 bg-danger p-12 pb-40 card-rounded flex-grow-1 bgi-no-repeat" style="background-position: calc(100% + 0.5rem) bottom; background-size: 35% auto; background-image: url(/theme/assets/media/svg/humans/custom-7.svg)">
                                <p class="text-inverse-danger pt-10 pb-5 font-size-h3 font-weight-bolder line-height-lg">Start with a branding
                                    <br>site design modern
                                    <br>content creation</p>
                                <a href="#" class="btn btn-warning font-weight-bold py-2 px-6">Join Now</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-xl-8">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 p-12 card-rounded bgi-no-repeat d-flex flex-column justify-content-center align-items-start" style="background-color: #FFF4DE; background-position: right bottom; background-size: auto 100%; background-image: url(/theme/assets/media/svg/humans/custom-8.svg)">
                                <h4 class="text-danger font-weight-bolder m-0">Join SAP now to get 35% off</h4>
                                <p class="text-dark-50 my-5 font-size-xl font-weight-bold">Start with a modern site design and customize it with your branding content,
                                    <br>and features. All Premium blogs include custom CSS.</p>
                                <a href="#" class="btn btn-danger font-weight-bold py-2 px-6">Join SaaS</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-4">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body p-0 d-flex">
                            <div class="d-flex align-items-start justify-content-start flex-grow-1 bg-light-warning p-8 card-rounded flex-grow-1 position-relative">
                                <div class="d-flex flex-column align-items-start flex-grow-1 h-100">
                                    <div class="p-1 flex-grow-1">
                                        <h4 class="text-warning font-weight-bolder">30 Days Free Trial</h4>
                                        <p class="text-dark-50 font-weight-bold mt-3">Pay 0$ for the First Month</p>
                                    </div>
                                    <a href="#" class="btn btn-link btn-link-warning font-weight-bold">Create Report
                                        <span class="svg-icon svg-icon-lg svg-icon-warning">

                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                                </g>
                                            </svg>

                                        </span></a>
                                </div>
                                <div class="position-absolute right-0 bottom-0 mr-5 overflow-hidden">
                                    <img src="/theme/assets/media/svg/humans/custom-13.svg" class="max-h-200px max-h-xl-275px mb-n20" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-xl-6">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 p-20 pb-40 card-rounded flex-grow-1 bgi-no-repeat" style="background-color: #1B283F; background-position: calc(100% + 0.5rem) bottom; background-size: 50% auto; background-image: url(/theme/assets/media/svg/humans/custom-10.svg)">
                                <h2 class="text-white pb-5 font-weight-bolder">Start Now</h2>
                                <p class="text-muted pb-5 font-size-h5">With our responsive themes and mobile
                                    <br>and desktop apps, enjoy a seamless
                                    <br>experience on any device so will your
                                    <br>blog's common visitors</p>
                                <a href="#" class="btn btn-danger font-weight-bold py-2 px-6">Join Now</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-6">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 p-20 pb-40 bg-warning card-rounded flex-grow-1 bgi-no-repeat" style="background-position: calc(100% + 0.5rem) bottom; background-size: 50% auto; background-image: url(/theme/assets/media/svg/humans/custom-10.svg)">
                                <h2 class="text-inverse-warning pb-5 font-weight-bolder">Start Now</h2>
                                <p class="text-inverse-warning pb-5 font-size-h5">With our responsive themes and mobile
                                    <br>and desktop apps, enjoy a seamless
                                    <br>experience on any device so will your
                                    <br>blog's common visitors</p>
                                <a href="#" class="btn btn-danger font-weight-bold py-2 px-6">Join Now</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-xl-6">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body d-flex p-0">
                            <div class="flex-grow-1 p-20 pb-40 card-rounded flex-grow-1 bgi-no-repeat" style="background-position: calc(100% + 0.5rem) bottom; background-size: 50% auto; background-image: url(/theme/assets/media/svg/humans/custom-10.svg)">
                                <h2 class="text-dark pb-5 font-weight-bolder">Start Now</h2>
                                <p class="text-dark-50 pb-5 font-size-h5">With our responsive themes and mobile
                                    <br>and desktop apps, enjoy a seamless
                                    <br>experience on any device so will your
                                    <br>blog's common visitors</p>
                                <a href="#" class="btn btn-danger font-weight-bold py-2 px-6">Join Now</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-6">

                    <div class="card card-custom card-stretch card-stretch-half gutter-b overflow-hidden">
                        <div class="card-body p-0 d-flex rounded bg-light-success">
                            <div class="py-18 px-12">
                                <h3 class="font-size-h1">
                                    <a href="#" class="text-dark font-weight-bolder">Nike Sneakers</a>
                                </h3>
                                <div class="font-size-h4 text-success">Get Amazing Nike Sneakers</div>
                            </div>
                            <div class="d-none d-md-flex flex-row-fluid bgi-no-repeat bgi-position-y-center bgi-position-x-left bgi-size-cover" style="transform: scale(1.5) rotate(-26deg); background-image: url('/theme/assets/media/products/13.png')"></div>
                        </div>
                    </div>


                    <div class="card card-custom card-stretch card-stretch-half gutter-b overflow-hidden">
                        <div class="card-body p-0 d-flex rounded bg-light-danger">
                            <div class="py-18 px-12">
                                <h3 class="font-size-h1">
                                    <a href="#" class="text-dark font-weight-bolder">Nike Sneakers</a>
                                </h3>
                                <div class="font-size-h4 text-danger">Get Amazing Nike Sneakers</div>
                            </div>
                            <div class="d-none d-md-flex flex-row-fluid bgi-no-repeat bgi-position-y-center bgi-position-x-left bgi-size-cover" style="transform: scale(1.5) rotate(-26deg); background-image: url('/theme/assets/media/products/12.png')"></div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-xl-8">


                    <div class="card card-custom card-stretch gutter-b">

                        <div class="card-body d-flex rounded bg-primary p-12 flex-column flex-md-row flex-lg-column flex-xxl-row">

                            <div class="bgi-no-repeat bgi-position-center bgi-size-cover h-300px h-md-auto h-lg-300px h-xxl-auto mw-100 w-550px" style="background-image: url('/theme/assets/media/products/12.png')"></div>


                            <div class="card card-custom w-auto w-md-300px w-lg-auto w-xxl-300px ml-auto">

                                <div class="card-body px-12 py-10">
                                    <h3 class="font-weight-bolder font-size-h2 mb-1">
                                        <a href="#" class="text-dark-75">Nike True Balance</a>
                                    </h3>
                                    <div class="text-primary font-size-h4 mb-9">$ 399.99</div>
                                    <div class="font-size-sm mb-8">Outlines keep you honest. They stop you from indulging in poorly ought out metaphorsy about driving and keep you focused one the overall structure of your post</div>

                                    <div class="d-flex mb-3">
                                        <span class="text-dark-50 flex-root font-weight-bold">Shoes Brand</span>
                                        <span class="text-dark flex-root font-weight-bold">Nike</span>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <span class="text-dark-50 flex-root font-weight-bold">SKU</span>
                                        <span class="text-dark flex-root font-weight-bold">NF3535</span>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <span class="text-dark-50 flex-root font-weight-bold">Color</span>
                                        <span class="text-dark flex-root font-weight-bold">White</span>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <span class="text-dark-50 flex-root font-weight-bold">Collection</span>
                                        <span class="text-dark flex-root font-weight-bold">2020 Spring</span>
                                    </div>
                                    <div class="d-flex">
                                        <span class="text-dark-50 flex-root font-weight-bold">In Stock</span>
                                        <span class="text-dark flex-root font-weight-bold">280</span>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                </div>
            </div>


            <div class="row">
                <div class="col-xl-8">

                    <div class="card card-custom card-stretch gutter-b">
                        <div class="card-body p-15 pb-20">
                            <div class="row mb-17">
                                <div class="col-xxl-5 mb-11 mb-xxl-0">

                                    <div class="card card-custom card-stretch">
                                        <div class="card-body p-0 rounded px-10 py-15 d-flex align-items-center justify-content-center" style="background-color: #FFCC69;">
                                            <img src="/theme/assets/media/products/21.png" class="mw-100 w-200px" style="transform: scale(1.6);">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-xxl-7 pl-xxl-11">
                                    <h2 class="font-weight-bolder text-dark mb-7" style="font-size: 32px;">Apple Earbuds Amazing Headset</h2>
                                    <div class="font-size-h2 mb-7 text-dark-50">From
                                        <span class="text-info font-weight-boldest ml-2">$299.00</span></div>
                                    <div class="line-height-xl">You also need to be able to accept that not every post is going to get your motor running. Some posts will feel like a chore, but if you have editorial control over what you write about, then choose topics you’d want to read – even if they relate to niche industries. The more excited you can be about your topic, the more excited your readers</div>
                                </div>
                            </div>
                            <div class="row mb-6">

                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">Brand</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">Nike Horizon</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">SKU</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">NF3535345</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">Color</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">Pure White</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">Collection</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">2020 Spring</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">In Stock</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">2770</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">Sold Items</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">280</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">Total Sales</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">$24,900</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">Net Profit</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">$3,750</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="mb-8 d-flex flex-column">
                                        <span class="text-dark font-weight-bold mb-4">Balance</span>
                                        <span class="text-muted font-weight-bolder font-size-lg">$68,300</span>
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex">
                                <button type="button" class="btn btn-primary font-weight-bolder mr-6 px-6 font-size-sm">
                                    <span class="svg-icon">

                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                <path d="M11,14 L9,14 C8.44771525,14 8,13.5522847 8,13 C8,12.4477153 8.44771525,12 9,12 L11,12 L11,10 C11,9.44771525 11.4477153,9 12,9 C12.5522847,9 13,9.44771525 13,10 L13,12 L15,12 C15.5522847,12 16,12.4477153 16,13 C16,13.5522847 15.5522847,14 15,14 L13,14 L13,16 C13,16.5522847 12.5522847,17 12,17 C11.4477153,17 11,16.5522847 11,16 L11,14 Z" fill="#000000"></path>
                                            </g>
                                        </svg>

                                    </span>New Stock</button>
                                <button type="button" class="btn btn-light-primary font-weight-bolder px-8 font-size-sm">
                                    <span class="svg-icon">

                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z M10.875,15.75 C11.1145833,15.75 11.3541667,15.6541667 11.5458333,15.4625 L15.3791667,11.6291667 C15.7625,11.2458333 15.7625,10.6708333 15.3791667,10.2875 C14.9958333,9.90416667 14.4208333,9.90416667 14.0375,10.2875 L10.875,13.45 L9.62916667,12.2041667 C9.29375,11.8208333 8.67083333,11.8208333 8.2875,12.2041667 C7.90416667,12.5875 7.90416667,13.1625 8.2875,13.5458333 L10.2041667,15.4625 C10.3958333,15.6541667 10.6354167,15.75 10.875,15.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"></path>
                                            </g>
                                        </svg>

                                    </span>Approve</button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-xl-8">

                    <div class="card card-custom">
                        <div class="card-body rounded p-0 d-flex bg-light">
                            <div class="d-flex flex-column flex-lg-row-auto w-auto w-lg-350px w-xl-450px w-xxl-650px py-10 py-md-14 px-10 px-md-20 pr-lg-0">
                                <h1 class="font-weight-bolder text-dark mb-0">Search Goods</h1>
                                <div class="font-size-h4 mb-8">Get Amazing Gadgets</div>

                                <form class="d-flex flex-center py-2 px-6 bg-white rounded">
                                    <span class="svg-icon svg-icon-lg svg-icon-primary">

                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"></path>
                                            </g>
                                        </svg>

                                    </span>
                                    <input type="text" class="form-control border-0 font-weight-bold pl-2" placeholder="Search Goods">
                                </form>

                            </div>
                            <div class="d-none d-md-flex flex-row-fluid bgi-no-repeat bgi-position-y-center bgi-position-x-left bgi-size-cover" style="background-image: url(/theme/assets/media/svg/illustrations/copy.svg);"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
<?php ob_end_flush(); ?>