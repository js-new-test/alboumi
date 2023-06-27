<div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $formTitle }}</h5>

                        <form id="updatePhotographerForm" class="col-md-10 mx-auto" method="post"
                            action="{{url('/admin/photgraphers/updatePhotographer')}}" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="photographer_id" value="{{ $photographerDetails['id'] }}">

                            @if(!empty($otherLanguages))
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="default_lang">Language :</label>
                                            </div>
                                            <div class="col-md-5">
                                                <select class="form-control multiselect-dropdown" name="language_id" id="defaultLanguage"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                                <input type="hidden" name="language_id" value="{{ $defaultLanguageId }}">
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="name"
                                                name="name" value="{{ $photographerDetails['name'] }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="location" name="location"value="{{ $photographerDetails['location'] }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="about">About</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <textarea class="form-control" id="about" name="about">{{ $photographerDetails['about'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row commonElement">
                                <div class="col-md-6">
                                    <label>Profile Picture</label><span class="text-danger">*</span>
                                    <input type="file" class="form-control" id="profile_pic" name="profile_pic" onchange="_showProfilePicDimensions(this)">
                                    <small class="form-text text-muted">Image size should be {{config('app.photographer_profile_pic.width')}} X {{config('app.photographer_profile_pic.height')}} px.</small>
                                    <small class="form-text text-muted">width = <small id="profile_width"></small></small>
                                    <small class="form-text text-muted">height = <small id="profile_height"></small></small>
                                    <input type="hidden" name="profile_image_height" id="profile_image_height">
                                    <input type="hidden" name="profile_image_width" id="profile_image_width">
                                </div>
                                <div class="col-md-6">
                                    <label>Cover Photo</label><span class="text-danger">*</span>
                                    <input type="file" class="form-control" id="cover_photo" name="cover_photo" onchange="_showCoverPicDimensions(this)">
                                    <small class="form-text text-muted">Image size should be {{config('app.photographer_cover_pic.width')}} X {{config('app.photographer_cover_pic.height')}} px.</small>
                                    <small class="form-text text-muted">width = <small id="cover_width"></small></small>
                                    <small class="form-text text-muted">height = <small id="cover_height"></small></small>
                                    <input type="hidden" name="cover_image_height" id="cover_image_height">
                                    <input type="hidden" name="cover_image_width" id="cover_image_width">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                       <img src="" width="100" height="100" class="mb-3" id="selected_profile_pic">
                                </div>
                                <div class="col-md-6">
                                       <img src="" width="100" height="100" class="mb-3" id="selected_cover_photo">
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="experience">Experience</label><span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="experience" name="experience"value="{{ $photographerDetails['experience'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 commonElement">
                                    <div class="form-group">
                                        <label for="web">Web</label>
                                        <div>
                                            <input type="text" class="form-control" id="web" name="web" value="{{ $photographerDetails['web'] }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row commonElement">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" @if(!empty($photographerDetails)) {{ ( $photographerDetails['status'] == 1 ) ? 'selected' : '' }} @endif>Active
                                                </option>
                                                <option value="0" @if(!empty($photographerDetails)) {{ ( $photographerDetails['status'] == 0 ) ? 'selected' : '' }} @endif>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="seo_title">SEO Title</label>
                                        <div>
                                            <input type="text" class="form-control" id="seo_title" name="seo_title" value="{{ $photographerDetails['seo_title'] }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="seo_description">SEO Description</label>
                                        <div>
                                            <textarea type="text" class="form-control" id="seo_description"
                                                name="seo_description">{{$photographerDetails['seo_description']}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="seo_keyword">SEO Keyword</label>
                                        <div>
                                            <input type="text" class="form-control" id="seo_keyword" name="seo_keyword" value="{{ $photographerDetails['seo_keyword'] }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100">Update</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/photgraphers') }}">
                                                    <button type="button" class="btn btn-light btn-shadow w-100"
                                                        name="cancel" value="Cancel">Cancel</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
