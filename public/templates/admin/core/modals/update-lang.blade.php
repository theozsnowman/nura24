<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-lang-{{ $lang->id }}" aria-hidden="true" id="update-lang-{{ $lang->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.config.langs.show', ['id' => $lang->id]) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update language') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Website short title') }}</label>
                                <input class="form-control" name="site_short_title" type="text" required value="{{ $lang->site_short_title ?? null }}" />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Homepage meta title') }}</label>
                                <input class="form-control" name="homepage_meta_title" type="text" required value="{{ $lang->homepage_meta_title ?? null }}" />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Homepage meta description') }}</label>
                                <input class="form-control" name="homepage_meta_description" type="text" value="{{ $lang->homepage_meta_description ?? null }}" />
                            </div>
                        </div>                      

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Language name') }}</label>
                                <input class="form-control" name="name" type="text" required value="{{ $lang->name }}" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Language code') }}</label>
                                <select name="code" class="form-control" required>
                                    <option selected value="">- {{ __('Select') }} -</option>
                                    @foreach($lang_codes_array as $key => $lang_code)
                                    @if($key == 'divider')<option disabled>-----------------</option>
                                    @else
                                    <option @if($lang->code == $lang_code) selected @endif value="{{ $lang_code }}">{{ $key }} ({{ $lang_code }})</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Locale') }}</label>
                                <select name="locale" class="form-control" required>
                                    <option @if (! $lang->locale) selected @endif value="">- {{ __('Select') }} -</option>
                                    @foreach($locales_array as $key => $locale)
                                        <option @if ($lang->locale == $key) selected @endif value="{{ $key }}">{{ $locale }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Is default language') }}</label>
                                <select name="is_default" class="form-control">
                                    <option @if ($lang->is_default == 1) selected @endif value="1">{{ __('Yes') }}</option>
                                    <option @if ($lang->is_default == 0) selected @endif value="0">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>
                     
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control" aria-describedby="activeFrontendHelp">
                                    <option @if ($lang->status == 'active') selected @endif value="active">{{ __('Active') }}</option>
                                    <option @if ($lang->status == 'inactive') selected @endif value="inactive">{{ __('Inactive') }}</option>
                                    <option @if ($lang->status == 'disabled') selected @endif value="disabled">{{ __('Disabled') }}</option>
                                </select>
                                <small id="activeFrontendHelp" class="form-text text-muted">{{ __('If there are more tnah one active language, a language selector will be active in website') }}</small>
                            </div>
                        </div>                           

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Timezone') }}</label>
                                <input class="form-control" name="timezone" type="text" aria-describedby="timeHelp" value="{{ $lang->timezone ?? null}}" />
                                <small id="timeHelp" class="form-text text-muted"><a target="_blank" href="http://php.net/manual/en/timezones.php">{{ __('View timezones list') }}</a></small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Date format') }}</label>
                                <select name="date_format" class="form-control">
                                    <option @if ($lang->date_format == '%e %B %Y') selected @endif value="%A, %e %B %Y">Monday, 30 December 2019</option>
                                    <option @if ($lang->date_format == '%e %B %Y') selected @endif value="%e %B %Y">30 December 2019</option>
                                    <option @if ($lang->date_format == '%e %b %Y') selected @endif value="%e %b %Y">30 Dec 2019</option>
                                    <option @if ($lang->date_format == '%B %e, %Y') selected @endif value="%B %e, %Y">December 30, 2019</option>
                                    <option @if ($lang->date_format == '%b. %e, %Y') selected @endif value="%b. %e, %Y">Dec. 30, 2019</option>
                                    <option @if ($lang->date_format == '%Y-%m-%d') selected @endif value="%Y-%m-%d">2019-12-30</option>
                                </select>
                            </div>
                        </div>

                        {{--
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Currency display style') }}</label>
                                <select name="currency_display_style" class="form-control">
                                    <option @if ($lang->currency_display_style == 'value_code') selected @endif value="value_code">{{ __('VALUE CODE') }} (18 USD)</option>
                                    <option @if ($lang->currency_display_style == 'code_value') selected @endif value="code_value">{{ __('CODE VALUE') }} (USD 18)</option>
                                    <option @if ($lang->currency_display_style == 'value_symbol') selected @endif value="value_symbol">{{ __('VALUE SYMBOL') }} (18 $)</option>
                                    <option @if ($lang->currency_display_style == 'symbol_value') selected @endif value="symbol_value">{{ __('SYMBOL VALUE') }} ($ 18)</option>
                                    <option @if ($lang->currency_display_style == 'value_name') selected @endif value="value_name">{{ __('VALUE NAME') }} (18 US Dollar)</option>
                                    <option @if ($lang->currency_display_style == 'name_value') selected @endif value="name_value">{{ __('NAME VALUE') }} (US Dollar 18)</option>
                                    <option @if ($lang->currency_display_style == 'condensed') selected @endif value="condensed">{{ __('Condensed') }} (USD18)</option>
                                </select>
                            </div>
                        </div>    
                        --}}                                                               

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Update language') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>