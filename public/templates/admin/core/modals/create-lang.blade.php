<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="create-lang" aria-hidden="true" id="create-lang">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="create-lang">{{ __('Add language') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Website short title') }}</label>
                                <input class="form-control" name="site_short_title" type="text" required />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Homepage meta title') }}</label>
                                <input class="form-control" name="homepage_meta_title" type="text" required />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Homepage meta description') }}</label>
                                <input class="form-control" name="homepage_meta_description" type="text" />
                            </div>
                        </div> 

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Language name') }}</label>
                                <input class="form-control" name="name" type="text" required />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Language code (2 characters)') }}</label>
                                <select name="code" class="form-control" required>
                                    <option selected value="">- {{ __('Select') }} -</option>
                                    @foreach($lang_codes_array as $key => $lang_code)
                                    @if($key == 'divider')<option disabled>-----------------</option>
                                    @else
                                    <option value="{{ $lang_code }}">{{ $key }} ({{ $lang_code }})</option>
                                    @endif
                                    @endforeach
                                </select>                                
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Locale') }}</label>
                                <select name="locale" class="form-control" required>
                                    <option selected value="">- {{ __('Select') }} -</option>
                                    @foreach($locales_array as $key => $locale)
                                        <option value="{{ $key }}">{{ $locale }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Is default language') }}</label>
                                <select name="is_default" class="form-control">
                                    <option value="1">{{ __('Yes') }}</option>
                                    <option selected="selected" value="0">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>                                  

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control" aria-describedby="activeFrontendHelp">
                                    <option value="active">{{ __('Active') }}</option>
                                    <option value="inactive">{{ __('Inactive') }}</option>
                                    <option value="disabled">{{ __('Disabled') }}</option>
                                </select>
                                <small id="activeFrontendHelp" class="form-text text-muted">{{ __('If there are more tnah one active language, a language selector will be active in website') }}</small>
                            </div>
                        </div>
                                                        

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Timezone') }}</label>
                                <input class="form-control" name="timezone" type="text" aria-describedby="timeHelp" />
                                <small id="timeHelp" class="form-text text-muted"><a target="_blank" href="http://php.net/manual/en/timezones.php">{{ __('View timezones list') }}</a></small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Date format') }}</label>
                                <select name="date_format" class="form-control">
                                    <option value="%A, %e %B %Y">Monday, 30 December 2019</option>
                                    <option value="%e %B %Y">30 December 2019</option>
                                    <option value="%e %b %Y">30 Dec 2019</option>
                                    <option value="%B %e, %Y">December 30, 2019</option>
                                    <option value="%b. %e, %Y">Dec. 30, 2019</option>
                                    <option value="%Y-%m-%d">2019-12-30</option>
                                </select>
                            </div>
                        </div>

                        {{--
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Currency display style') }}</label>
                                <select name="currency_display_style" class="form-control">
                                    <option value="value_code">{{ __('VALUE CODE') }} (18 USD)</option>
                                    <option value="code_value">{{ __('CODE VALUE') }} (USD 18)</option>
                                    <option value="value_symbol">{{ __('VALUE SYMBOL') }} (18 $)</option>
                                    <option value="symbol_value">{{ __('SYMBOL VALUE') }} ($ 18)</option>
                                    <option value="value_name">{{ __('VALUE NAME') }} (18 US Dollar)</option>
                                    <option value="name_value">{{ __('NAME VALUE') }} (US Dollar 18)</option>
                                    <option value="condensed">{{ __('Condensed') }} (USD18)</option>
                                </select>
                            </div>
                        </div>   
                        --}}                    

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Add language') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>