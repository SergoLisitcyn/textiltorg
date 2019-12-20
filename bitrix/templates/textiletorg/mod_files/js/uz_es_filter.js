
var uzAjaxFilter = {
    debug: 0,
    debugBox: "uz_debug",
    langData: "ru",
    frontBaseHref: "",
    dataSource: "",

    showMinMaxIntervalValues: 0,
    intervalStepMode: "default",
    intervalStepConstant: 100,

    forceFieldsMinimize: 0,
    showSelectedValsWhenFieldIsMinimized: 0,
    maxValsToShowWhenFieldIsMinimized: 1,
    roundPrec: 2,

    fldCaptionQtyTpl: "@@qty@@",

    fldValsQtyTpl: "&nbsp(@@qty@@)",

    aMatchPatterns: {
        "process": "Matched <img src='_img/uz_request_indicator.gif'> products",
        "process1": "Matched @@cnt@@ product",
        "process2": "Matched @@cnt@@ products",
        "process5": "Matched @@cnt@@ products"
    },
    matchedBoxClassName: "uz_matched_cnt_box",

    //hideEmpty: true,
    //autoSubmit: false,

    optionsDisableMethod: "erase", //disable|erase

    userAgentType: '',
    hGetVars: [],

    aFields: [], // Filter fields names
    /*
    {
        name: '',
        type: '',   // select|checkbox|radio|text|interval|val_from|val_to
        multi: '##multiple##'
        val: '',
        defaultVal: 'text',0
        defaultDisabled: false, // true|false
    }
    //*/

    //aDefaultFieldsDisabledStatus: {},

    defaultDisplay: "inline",

    //selectCatSupport: 0,
    //catSelectId: "select_cat_id",
    //scriptLink: "",
    //currentCatId: 0,
    //aCatsSublinks: new Array(),

    filterFormName: "",
    oForm: null,


    // Requests number
    requestId: 0,

    // Requests queue
    aRequests: [],


    // Style for disabled elements
    disabledClass: "uz_es_filter_label_disabled",
    disabledClass2: "uz_es_filter_div_disabled",

    // Class name for hide field values box
    hideValuesClass: "uz_filter_box_vals_hidden",
    // Field values box id postfix
    fieldValuesBoxIdPostfix: "_vals_box",

    fieldCaptionValuesBoxIdPostfix: "_capt_val",



    intervalObjectIdPostfix: "_interval_slider",

    hSlidersIsInit: {},


    //
    // Init section
    //

    init: function(hOpt){

        // Detect domain
        //this.frontBaseHref = "http://"+document.domain+"/"; (create mixed content)
        this.frontBaseHref = "//"+document.domain+"/";
		$('.spoiler-body').hide();
		$('.uz_filter_field_box > .spoiler-title').click(function(){
	    $(this).toggleClass('opened').toggleClass('closed').next().slideToggle();
		});
        //
        // Set default form name
        //
        if(typeof(_cms_document_form) != 'undefined'){
            this.filterFormName = _cms_document_form;
        }

        // Set options
        if(typeof(hOpt) != 'undefined'){
            this.setOptions(hOpt);
        }

        // Make unique IDs
        $("form[name=" + this.filterFormName + "] input").each(function() {
            if(uzAjaxFilter.checkMinVer('1.6')) {
                $(this).prop('id', uzAjaxFilter.filterFormName + '_' + $(this).prop('id'));
            } else {
                $(this).attr('id', uzAjaxFilter.filterFormName + '_' + $(this).attr('id'));
            }
        });

        // Replace labels
        $("form[name=" + this.filterFormName + "] label").each(function() {
            if(uzAjaxFilter.checkMinVer('1.6')) {
                $(this).prop('for', uzAjaxFilter.filterFormName + '_' + $(this).prop('for'));
            } else {
                $(this).attr('for', uzAjaxFilter.filterFormName + '_' + $(this).attr('for'));
            }
        });

        // Enable debug by ip
        if(typeof(DEBUG_BY_IP) != 'undefined'){
            if(DEBUG_BY_IP == 1){
                this.debug = 1;
            }
        }


        //
        // Fix Google Chrome bug witn re-constructed 'select' element after use browser 'back' button.
        //
        var ag = navigator.userAgent;
        this.userAgentType = "normal";
        this.userAgentType = (navigator.userAgent.search('Chrome') != -1) ? "Chrome" : this.userAgentType;
        this.userAgentType = (navigator.userAgent.search('Safari') != -1) ? "Safari" : this.userAgentType;
        this.userAgentType = (navigator.userAgent.search('Opera') != -1) ? "Opera" : this.userAgentType;
        //this.addToDebug("userAgentType = "+this.userAgentType);
        //this.addToDebug("userAgent = "+navigator.userAgent);
        //console.debug(this.userAgentType);


        this.hGetVars = [];
        var aTmp = document.location.href.split('?');
        if(aTmp.length > 1){
            //console.debug('1');
            var aParams = aTmp[1].split('&');
            for(i = 0; i < aParams.length; i++){
                //console.debug('2 - '+aParams[i]);
                var aFld = aParams[i].split('=');
                if(aFld[0].length){
                    //console.debug('3');
                    //this.hGetVars[aFld[0]] = decodeURIComponent(aFld[1]);
                    var val = aFld[1];
                    val = val.replace(/\+/g, " ");    // Hack google chrome bug witn url decode
                    val = decodeURIComponent(val);
                    this.hGetVars[aFld[0]] = val;
                }
            }
        }
        //console.debug(this.hGetVars);
        //this.addToDebug("\n\nhGetVars = "+this.toString(this.hGetVars)+"\n\n");


        //
        // Get form object
        //
        if(typeof(document.forms[this.filterFormName]) != 'undefined'){
            this.oForm = document.forms[this.filterFormName];
        }

        //
        // Init fields current values
        //
        this.initFieldsState();

        this.onLoadShowFieldsValues();
        //this.getFieldsDefaultDisabled();

        //if(this.selectCatSupport){
        //    this.initCurrentCat();
        //}

        //
        // Set messages handlers
        //
        if(typeof(AMI) != 'undefined'){
            if(typeof(AMI.Message) != 'undefined'){
                AMI.Message.addListener("filter_field_changed", uzAjaxFilter.onMessageFilterFieldChanged);
                AMI.Message.addListener("filter_field_caption_click", uzAjaxFilter.onMessageFilterFieldCaptionClick);
                AMI.Message.addListener("filter_field_caption_desc_click", uzAjaxFilter.onMessageFilterFieldCaptionDescClick);
                AMI.Message.addListener("filter_field_interval_change", uzAjaxFilter.onMessageFilterFieldIntervalChange);
            }
        }



        //
        // On load - get data and populate fields
        //
        this.onFieldChange(false);

    },

    setOptions: function(opt){
        for(var key in opt){
            this[key] = opt[key];
        }
    },

    initFieldsState: function(){
        if(this.oForm != null && this.aFields.length){
            var i, val;

            //console.debug("this.hGetVars = "+this.toString(this.hGetVars));

            //alert(this.toString(this.aFields));
            for(i = 0; i < this.aFields.length; i++){
                //var name = this.aFields[i].formName;
                //name = name.replace(/\[\]/, '');
                //this.aFields[i].name = name;
                this.aFields[i].defaultVal = this.getFieldValue(this.aFields[i].formName);
            }
        }
        //alert(this.toString(this.aFields));
        //console.debug(uzAjaxFilter.toString(this.aFields));
    },

    //
    // Init section end
    //



    //
    // Request data section
    //

    onMessageFilterFieldChanged: function(isManualChange, p2){
        uzAjaxFilter.onFieldChange(isManualChange);
        return true;
    },

    reset: function(){
        this.onFieldChange(false, true);
    },

    onFieldChange: function(isManualChange, forceEmptyVals){
        //this.addToDebug("called onFieldChange "+((isManualChange) ? 1:0));
        //console.log(this.filterFormName);

        //
        // Prevent request if form is missing or have no fields
        //
        if(this.oForm == null || this.aFields.length == 0){
            return true;
        }

        /*
        if(typeof(isManualChange) != 'undefined'){
            if(isManualChange == 1){
                if(this.autoSubmit){
                    if(typeof(CheckFilterForms) != 'undefined'){
                        CheckFilterForms(this.oForm, 0);
                    }
                    return;
                }
            }
        }
        //*/

        //
        // Prepare request data
        //
        var hRequest = {};

        //
        // Prepare subpath
        //
        var aTmp = active_module_link.split('?');
        var subpath = aTmp[0].substr(this.frontBaseHref.length);
        hRequest["subpath"] = subpath;

        // Prepare search subcats flag
        var searchSubCats = $("#" + this.filterFormName + "_flt_search_subcats:checked").length? 1: 0;
        hRequest["search_subcats"] = searchSubCats;

        // Prepare category id
        var catId = 20000;
        if(typeof(this.oForm.catid) != 'undefined'){
            // Apply selected cat
            if(this.selectCatSupport && document.getElementById(this.catSelectId)){
                catId = document.getElementById(this.catSelectId).value;
                this.oForm.catid.value = catId;
            } else {
                catId = this.oForm.catid.value;
            }
        }
        hRequest["catid"] = catId;

        // Set submit url to selected category
        this.setForcedSubmitUrl(catId);

        // Reset other fields when category changed
        //var forceEmptyVals = 0;
        /*
        if(this.selectCatSupport && this.currentCatId != catId){
            forceEmptyVals = 1;
            // Store new cat id
            this.currentCatId = catId;
        }
        //*/

        //alert(uzAjaxFilter.toString(this.aFields));

        // Collect actual fields values from form
        if(this.aFields.length){
            for(var i = 0; i < this.aFields.length; i++){
                if(forceEmptyVals){
                    if(this.aFields[i].type == 'checkbox') {
                        hRequest[this.aFields[i].name] = [""];
                    } else {
                        hRequest[this.aFields[i].name] = "";
                    }
                } else {
                    //alert(this.toString(this.aFields));
                    hRequest[this.aFields[i].name] = this.getFieldValue(this.aFields[i].formName);
                    //alert(this.aFields[i].name + '=' +this.getFieldValue(this.aFields[i].formName));
                    //this.addToDebug("\n\nhRequest[this.aFields[i].name] = "+hRequest[this.aFields[i].name]+"<br>\n");
                }
                //alert(this.aFields[i].name + '=' + hRequest[this.aFields[i].name]);
            }
        }
        //console.log("hRequest values");
        //console.log(this.toString(hRequest));

        //var fieldsList = this.getFieldsList();
        hRequest["fields_list"] = this.aFields;
        //console.debug(hRequest);
        //this.addToDebug("\n\nhRequest = "+this.toString(hRequest)+"\n\n");

        this.populateMatchedCount();

        this.disableFields();

        this.requestData(hRequest);

    },

    requestData: function(hData) {
        var urlParams = "";

        //urlParams += this.prepareUrlParams(hData);

        //
        // Erase form field names from request
        //
        hDataTmp = this.cloneObject(hData);
        if(hDataTmp["fields_list"].length){
            for(var i =0; i < hDataTmp["fields_list"].length; i++){
                hDataTmp["fields_list"][i].formName = "";
            }
        }
        //alert(this.toString(hDataTmp));
        //this.addToDebug("hDataTmp = "+this.toString(hDataTmp)+"<br>\n\n");
        //this.addToDebug("toJsonString hDataTmp = "+this.toJsonString(hDataTmp)+"<br>\n\n");


        //alert(urlParams);
        /*
        for(name in hData){
            if(name == "plainUrlParams"){
                urlParams += hData[name];
            } else {
                urlParams += "&" + name + "=" + hData[name];
            }
        }
        //*/

        // Increment id
        this.requestId++;

        // Prepare request data
        var hRequest = {
            id: this.requestId,
            lang_data: this.langData,
            data: this.toJsonString(hDataTmp)
        }
        //this.addToDebug("this.requestId = "+this.requestId);
        //alert(hRequest.data);
        //this.addToDebug("hRequest.data = "+hRequest.data);
        //alert(this.frontBaseHref + this.dataSource+"\n\n");

        if(typeof(AMI) != 'undefined'){
            if(typeof(AMI.Message) != 'undefined'){
                AMI.Message.send("uz_on_filter_request", hRequest, false);
            }
        }

        //this.addToDebug("\n\nhRequest = "+this.toString(hRequest)+"\n\n");
$('#debug_zapros').html(hRequest['data']);
        jQuery.ajax({
            type: "POST",
            cache: false,
            async: true,
            url: this.frontBaseHref + this.dataSource,
            data: hRequest,
            dataType: "json",
            success: uzAjaxFilter.requestCbSuccess,
            error: uzAjaxFilter.requestCbError
        });
    },

    requestCbSuccess: function(content) {
        //alert("requestCbSuccess");
        //uzAjaxFilter.addToDebug(content);
		$('#debug_txt').html(content['data']);
        uzAjaxFilter.precessResponse(content);
    },

    requestCbError: function(event, jqXHR, ajaxSettings){
      /*
        uzAjaxFilter.actionProcess = 0;
        uzAjaxFilter.addToDebug("requestCbError\n\n");
        uzAjaxFilter.addToDebug("event: "+uzAjaxFilter.toString(event)+"\n\n");
        uzAjaxFilter.addToDebug("jqXHR: "+uzAjaxFilter.toString(jqXHR)+"\n\n");
        uzAjaxFilter.addToDebug("ajaxSettings: "+uzAjaxFilter.toString(ajaxSettings)+"\n\n");
        */
    },

    precessResponse: function(hResp){
        //console.debug("hResp:");
        //console.debug(hResp);
        //this.addToDebug("\n\hResp = "+this.toString(hResp)+"\n\n");
        //this.actionProcess = 0;
        //var hResp = AMI.String.decodeJSON(content);
        if(this.debug && typeof(hResp.debug) != 'undefined'){
            if(document.getElementById(this.debugBox)){

                if(typeof(hResp.data.debug) != 'undefined'){
                    hResp.debug = hResp.data.debug + "<br><br>" + hResp.debug;
                }

                //hResp.debug = hResp.debug + "hResp:<pre>\n"+this.toString(hResp)+"</pre>";

                //this.addToDebug("\n\nhResp.debug = "+hResp.debug+"\n\n");
                this.addToDebug(hResp.debug, true);
                //document.getElementById(this.debugBox).innerHTML = hResp.debug;
            }
        }
        if(typeof(hResp.data.id) != 'undefined'){
            if(hResp.data.id == this.requestId){
                //alert(this.toString(hResp.data));
                this.applyFilterData(hResp.data);
            }
        }
        //alert(this.toString(content));
    },

    getFieldValue: function(name){
        var i, val;
        var res = '';
        //this.addToDebug("getFieldValue "+name+" ");

        //console.log("getFieldValue "+name);

        if(this.oForm != null && this.aFields.length){
            for(i = 0; i < this.aFields.length; i++){

                if(this.aFields[i].formName == name){
                    //console.log("1");
                    //alert(this.aFields[i].formName+" == "+name);
                    switch(this.aFields[i].type){
                        //select|checkbox|radio|text|interval|val_from|val_to
                        case "checkbox":
                            if(this.aFields[i].formName.indexOf("[]") < 0){
                                //alert("Single checkbox");
                                // Single checkbox
                                res = $("#" + this.filterFormName + "_" + name + ":checked").length;
                            } else {
                                // Multi checkboxes
                                var j = 0;
                                var cName = "chk_"+this.aFields[i].name+"_";
                                var res = [];
                                while(j <= this.aFields[i].flagsCnt){
                                    if($("#" + this.filterFormName + "_" + cName + j + ":checked").length) {
                                        res.push($("#" + this.filterFormName + "_" + cName + j + ":checked").val()+"");
                                    }
                                    j++;
                                }
                            }
                            break;
                        case "radio":
                            var j = 0;
                            var cName = "rd_"+this.aFields[i].formName+"_";

                            //var aCheckedRadio = $("#" + this.filterFormName + "_" + cName + j + ":checked");
                            var aCheckedRadio = $("form[name='"+this.filterFormName+"'] input[name='"+this.aFields[i].formName+"']:checked");
                            //aChecks = AMI.$("form[name='"+oForm.name+"'] input[name='"+hField.form_name+"[]']:checked");

                            //console.log("radio cName = " + cName + ", pattern = form[name='"+this.filterFormName+"'] input[name='"+this.aFields[i].formName+"']:checked");

                            if(aCheckedRadio.length){
                                res = $(aCheckedRadio[0]).val()+"";
                                //console.log("found checked val "+res);
                            } else {
                                //console.log("not found checked val");
                            }
                            /*
                            // TODO. Verify code
                            $("#" + this.filterFormName + "_" + cName + j + ":checked").each(function() {
                                res = $("#" + this.filterFormName + "_" + cName + j + ":checked").val();
                                j++;
                            });
                            */
                            break;
                        case "select":
                            if(this.aFields[i].multi == ''){
                                //
                                // Fix Google Chrome bug witn re-constructed 'select' element after use browser 'back' button.
                                //
                                var bugedAgent = false;
                                if(this.userAgentType == "Chrome" || this.userAgentType == "Safari"){
                                    bugedAgent = true;
                                }
                                if(bugedAgent && this.requestId == 0 && typeof(this.hGetVars[name]) != 'undefined'){
                                    res = this.hGetVars[name];
                                } else {
                                    res = this.oForm.elements[name].value;
                                }
                            } else {
                                var res = [];
                                var oSel = this.oForm.elements[name];
                                for(var j = 0; j < oSel.options.length; j++){
                                    if(oSel.options[j].selected && oSel.options[j].style.display != "none"){
                                        res.push(oSel.options[j].value+"");
                                    }
                                }
                            }
                            break;
                        case "interval":
                            var res = [$("#" + this.filterFormName + "_" + this.aFields[i].name + "_from").val()+"", $("#" + this.filterFormName + "_"+this.aFields[i].name + "_to").val()+""];
                            break;
                        default:
                            //console.log("2");
                            //res = $("form [name='"+this.filterFormName+"']").find("[name='"+name+"']").val();
                            //res = this.oForm.elements[this.aFields[i].formName].value;
                            for(var j=0; j < this.oForm.elements.length; j++){
                                //console.log(this.oForm.elements[j].name+" = "+this.oForm.elements[j].value);
                                if(this.oForm.elements[j].name == name){
                                    res = this.oForm.elements[j].value+"";
                                }
                            }
                            /*
                            if(typeof(this.oForm[name])){
                                //console.log("3");
                                res = this.oForm[name].value;
                            } else {
                                //console.log("4");
                                res = $("#" + this.filterFormName + "_" + this.aFields[i].formName).val();
                            }
                            */
                            //console.log("res = "+res);
                            break;
                    }
                }
            }
        }
        //this.addToDebug(res+"<br>\n");
        return res;
    },

    //
    // Request data section end
    //




    getFieldProp: function(name) {
        if(this.aFields.length){
            for(var i = 0; i < this.aFields.length; i++){
                if(name == this.aFields[i].name){
                    return this.aFields[i];
                }
            }
        }
        return {};
    },

    applyFilterData: function(hResp) {
        if(this.debug){
            //alert(content);
            if(typeof(hResp.debug) != 'undefined'){
                if(hResp.debug.length){
                    //alert(hResp.debug);
                }
            }
        }

        //console.debug(hResp);

        //
        // Apply matched items count
        //
        this.populateMatchedCount(hResp.cnt);

        //
        // Apply fields data
        //
        if(typeof(hResp.fields_data) != 'undefined'){
            if(hResp.fields_data.length){
                for(var i = 0; i < hResp.fields_data.length; i++){
                    this.populateField(hResp.fields_data[i]);
                }
            }
        }

        this.enableFields();

        AMI.Message.send("on_uz_filter_fields_applied");

    },

    populateMatchedCount: function(cnt) {
        var patternPostfix = '';
        var mod = cnt % 10;
        if(typeof(cnt) != 'undefined'){
            if(cnt == 0) {
                patternPostfix = 5;
            } else if (cnt == 1 || (cnt > 20 && mod == 1)) {
                patternPostfix = 1;
            } else if ((cnt >= 2 && cnt <= 4) ||(cnt > 20 && mod >= 2 && mod <= 4)) {
                patternPostfix = 2;
            } else {
                patternPostfix = 5;
            }
        } else {
            var cnt = -1;
        }
        this.applyMatchedCount("process"+patternPostfix, {cnt: cnt});

        var hEvent = {
            cnt: cnt,
            patternName: "process"+patternPostfix
        };
        //console.log("call on_uz_filter_populate_matched_count");
        AMI.Message.send("on_uz_filter_populate_matched_count", hEvent);
    },

    applyMatchedCount: function(patternName, hData) {
        var str = "";
        if(typeof(AMI) != 'undefined'){
            if(typeof(AMI.Template) != 'undefined' && typeof(this.aMatchPatterns[patternName]) != 'undefined'){
                str = AMI.Template.parse(this.aMatchPatterns[patternName], hData);
            }
        }
        $("."+this.matchedBoxClassName).html(str);
    },

    populateField: function(hData) {
        var name = hData.name;
        var hProp = this.getFieldProp(name);
        if(uzAjaxFilter.debug){
            //alert("populateField name="+name+", with prop "+this.toString(hProp)+"\n hData: "+this.toString(hData));
            //alert("populateField "+hProp.name+" - "+hProp.type);
        }

        if(typeof(hProp.type) != 'undefined'){
            switch(hProp.type){
                case "select":
                    if(typeof(hProp.multi) == 'undefined' || hProp.multi == ''){
                        this.populateSelect(hData);
                    } else {
                        this.populateSelectMulti(hData);
                    }
                    break;
                case "checkbox":
                    this.populateCheckbox(hData);
                    break;
                case "radio":
                    this.populateRadio(hData);
                    break;
                case "price_from":
                case "price_to":
                    this.populateText(hData);
                    break;
                case "text":
                    this.populateText(hData);
                    break;
                case "interval":
                    this.populateInterval(hData);
                    break;
                default:
                    break;
            }

            AMI.Message.send('uz_on_field_populated', hProp.type, hData);
        }
    },

    populateSelect: function(hData) {
        var hProp = this.getFieldProp(hData.name);
        var name = hProp.formName;
        var defaultVal = hData.selected_vals;
        var aVals = hData.exist_vals;

        var placeholderText = "";
        if(typeof(hProp.emptyValPlaceholder) != "undefined"){
            placeholderText = hProp.emptyValPlaceholder;
        }

        AMI.Message.send('uz_es_filter_populate_select', hProp, hData);

        if(this.debug){
            //alert("populate select, aVals = "+this.toString(aVals)+"\n defaultVal = "+defaultVal);
            //alert("populateField "+hProp.name+" - "+hProp.type);
        }

        //console.debug(this.oForm.elements);

        var el = this.oForm.elements[name];

        var i, n;

        if(this.debug){
            //alert(data);
            //alert(name);
            //alert(aVals.length);
            //alert(aVals)
        }

        if(this.optionsDisableMethod == "erase"){
            // Drop old options
            n = el.length;
            //value = el.value;
            var tmpOption = $(el.options[0]).attr("text")? $(el.options[0]).attr("text"): $(el.options[0]).prop("text");
            if(placeholderText != ""){
                tmpOption = placeholderText;
            }
            //console.debug("tmpOption = '"+tmpOption+"'");

            //console.debug("len = "+el.options.length);
            //console.debug("default val = "+$(el).val());

            /*
            for(i=n-1; i>0; i--) {
                //el.options[i] = null;
                $(el).find(":last").remove();
            }
            */
            $(el).empty();
            //alert("options erased");


            $(el).append( $('<option value="">'+tmpOption+'</option>'));

            if(aVals.length){
                // Add new options
                for(i=0; i<aVals.length; i++) {
                    //el.options[i+1] = new Option(aVals[i], aVals[i]);
                    $(el).append( $('<option value="'+this.escapeHtml(aVals[i])+'">'+aVals[i]+'</option>'));
                }
                el.value = defaultVal;

            } else {
                el.value = "";
            }
            //alert("options added");

        } else {

            for(i=1; i<el.length; i++) {

                var isExists = this.inArray(el.options[i].value, aVals) ? 1 : 0;
                /*
                if(aVals.length){
                    for(j=0; j<aVals.length; j++) {
                        //alert("aVals[j] = "+aVals[j]+", el.options[i].value = "+el.options[i].value);
                        if(aVals[j] == el.options[i].value){
                            isOn = true;
                        }
                    }
                }
                //*/

                if(isExists){
                    //alert(el.options[i].value+" is on");
                    //el.options[i].style.display = "";
                    el.options[i].disabled = false;
                } else {
                    //el.options[i].style.display = "none";
                    el.options[i].disabled = true;
                }
            }
        }

        if(aVals.length){
            // Show field
            if(document.getElementById("fld_box_"+name)){
                document.getElementById("fld_box_"+name).style.display = this.defaultDisplay;//"inline";
            }


        } else {
            el.value = "";

            // Hide field
            if(this.hideEmpty){
                if(document.getElementById("fld_box_"+name)){
                    document.getElementById("fld_box_"+name).style.display = "none";
                }
            }
        }

    },

    populateSelectMulti: function(hData) {
        var hProp = this.getFieldProp(hData.name);
        var name = hProp.formName;
        var aDefaultVals = hData.selected_vals;
        var aVals = hData.exist_vals;

        AMI.Message.send('uz_es_filter_populate_select_multi', hProp, hData);

        var el = this.oForm.elements[name];

        var i, n;

        if(uzAjaxFilter.debug){
            //alert(data);
            //alert(name);
            //alert(aVals.length);
            //alert(aVals)
        }

        /*
        if(this.optionsDisableMethod == "erase"){
            // Drop old options
            n = el.length;
            //value = el.value;
            for(i=n-1; i>0; i--) {
                el.options[i] = null;
            }

            if(aVals.length){
                // Add new options
                for(i=0; i<aVals.length; i++) {
                    el.options[i+1] = new Option(aVals[i], aVals[i]);
                    if(this.inArray(aVals[i], aDefaultVals)){
                        el.options[i+1].selected = true;
                        selectedCnt++;
                    }
                }
            }

        } else {
        //*/
            var selectedCnt = 0;

            for(i=1; i<el.length; i++) {

                isExists = this.inArray(el.options[i].value, aVals) ? 1 : 0;
                /*
                var isOn = false;
                if(aVals.length){
                    for(j=0; j<aVals.length; j++) {
                        if(aVals[j] == el.options[i].value){
                            isOn = true;
                        }
                    }
                }
                //*/
                if(isExists){
                    //el.options[i].style.display = "";
                    el.options[i].disabled = false;

                    if(el.options[i].selected){
                        selectedCnt++;
                    }
                    //el.options[j].disabled = false;
                } else {
                    //alert(el.options[i].value+" is off");
                    //el.options[i].style.display = "none";
                    el.options[i].selected = false;
                    el.options[i].disabled = true;
                }
            }
        //}


        if(aVals.length){
            // Show field
            if(document.getElementById("fld_box_"+name)){
                document.getElementById("fld_box_"+name).style.display = this.defaultDisplay;//"inline";
            }

            if(selectedCnt == 0){
                el.options[0].selected = true;
                el.value = "";
            }
        } else {
            el.options[0].selected = true;
            el.value = "";

            // Hide field
            if(this.hideEmpty){
                if(document.getElementById("fld_box_"+name)){
                    document.getElementById("fld_box_"+name).style.display = "none";
                }
            }
        }
    },

    populateCheckbox: function(hData) {
        if(uzAjaxFilter.debug){
            //alert(data);
        }
        var hProp = this.getFieldProp(hData.name);
        var name = hProp.formName;
        var defaultVal = hData.selected_vals;
        var aVals = hData.exist_vals;

        AMI.Message.send('uz_es_filter_populate_checkbox', hProp, hData);

        //alert(typeof(aVals));
        //console.debug(aValsQty);
        //alert("populateCheckbox, form name = "+name+", aVals = "+this.toString(aVals)+"\n defaultVal = "+defaultVal);

        var i, j, val, oInput, isExists, cName, qty;

        if(name.indexOf("[]") <= 0){
            // Single checkbox. TODO. Validate code
            $("#" + this.filterFormName + "_" + name).checked = !(defaultVal == '0');
        } else {
            // Walk over checkboxes
            //chk_ext_custom_29[]_0
            i = 0;
            cName = "chk_"+hProp.name+"_"+i;
            if(uzAjaxFilter.debug){
                //alert("check field '"+cName+"'");
            }
            while(i <= hProp.flagsCnt){
                if($("#" + this.filterFormName + "_" + cName).length){
                    val = $("#" + this.filterFormName + "_" + cName).val();
                    var nameForLabel = cName.replace(/\[\]/, '');

                    //
                    // Check is exists value
                    //
                    if(this.inArray(val, aVals)){
                        // Enable checkbox
                        this.enableLabel(nameForLabel);

                        // Update qty
                        if(typeof(hData.vals_qty) != 'undefined'){
                            var qty = 0;
                            if(typeof(hData.vals_qty[val]) != 'undefined'){
                                qty = hData.vals_qty[val];
                            }
                            this.updateLabelQty(nameForLabel, qty);
                        }
                    } else {
                        // Disable checkbox
                        if(i > 0){
                            this.disableLabel(nameForLabel);

                            // Update qty
                            if(typeof(hData.vals_qty) != 'undefined'){
                                this.updateLabelQty(nameForLabel, 0);
                            }
                        }
                    }

                    if(uzAjaxFilter.checkMinVer('1.6')) {
                        $("#" + this.filterFormName + "_" + cName).prop('checked', this.inArray(val, defaultVal));
                    } else {
                        $("#" + this.filterFormName + "_" + cName).attr('checked', this.inArray(val, defaultVal)? 'checked': '');
                    }

                }
                i++;
                cName = "chk_"+hProp.name+"_"+i;
            }
        }

    },


    populateRadio: function(hData) {
        if(uzAjaxFilter.debug){
            //alert(this.toString(hData));
        }
        var hProp = this.getFieldProp(hData.name);
        var name = hProp.formName;
        var defaultVal = hData.selected_vals;
        var aVals = hData.exist_vals;

        AMI.Message.send('uz_es_filter_populate_radio', hProp, hData);

        //alert("populateRadio, form name = "+name+", aVals = "+this.toString(aVals)+"\n defaultVal = "+defaultVal);

        var i, j, val, oInput, isExists, cName;

        //
        // Walk over radio inputs
        //chk_ext_custom_29[]_0
        //
        i = 0;
        cName = "rd_"+name+"_"+i;
        while($("#" + this.filterFormName + "_" + cName).length){
            // TODO. Verify code
            oInput = $("#" + this.filterFormName + "_" + cName);
            val = oInput.val();

            isExists = this.inArray(val, aVals) ? 1 : 0;

            if(isExists){
                // Enable radio
                this.enableLabel(cName);

                // Update qty
                if(typeof(hData.vals_qty) != 'undefined'){
                    var qty = 0;
                    if(typeof(hData.vals_qty[val]) != 'undefined'){
                        qty = hData.vals_qty[val];
                    }
                    this.updateLabelQty(cName, qty);
                }

                //
                // Check default value
                //
            } else {
                // Disable radio
                if(i > 0){
                    this.disableLabel(cName);

                    // Update qty
                    if(typeof(hData.vals_qty) != 'undefined'){
                        this.updateLabelQty(cName, 0);
                    }
                }
            }

            //
            // Selected current value
            //
            if(val == defaultVal){
                oInput.checked = true;
            }

            i++;
            cName = "rd_"+name+"_"+i;
        }
    },


    populateText: function(hData) {
        var hProp = this.getFieldProp(hData.name);
        var name = hProp.formName;

        AMI.Message.send('uz_es_filter_populate_text', hProp, hData);

        var defaultVal = hData.selected_vals;

        var hProp = uzAjaxFilter.getFieldProp(name);

        //var el = this.oForm.elements[name];
        //el.value = defaultVal;
        for(var i = 0; i < this.oForm.elements.length; i++){
            if(this.oForm.elements[i].name == name){
                this.oForm.elements[i].value = defaultVal;
            }
        }
    },

    populateInterval: function(hData) {
         //console.log("called populateInterval, data:");
         //console.log(this.toString(hData));

        //var step = hData.step;
        var min = hData.super_interval.min * 1;
        var max = hData.super_interval.max * 1;
        if(max < min){
            max = min;
        }
        //console.log("selected_vals:");
        //console.log(this.toJsonString(hData.selected_vals));
        var selMin = this.validateFloat(hData.selected_vals[0]);
        var selMax = this.validateFloat(hData.selected_vals[1]);

        if(selMin < min){
            selMin = min;
        }
        if(selMin > max){
            selMin = max;
        }
        if(selMax < min){
            selMax = min;
        }
        if(selMax > max){
            selMax = max;
        }
        //alert(selMin+", "+selMax);
        //console.log("selMin = "+selMin+", selMax = "+selMax);

        hInterval = {
            step: hData.step,
            superInterval: {min: min, max: max},
            selectedVals: {min: selMin, max: selMax},
            existsVals: {min: 0, max: 1}
        };

        //console.log("hInterval:");
        //console.log(this.toString(hInterval));

        //console.debug(hData);
        //alert("hData:\n"+this.toString(hData)+"\n\nhInterval:\n"+this.toString(hInterval));

        this.initInterval(hData.name, hInterval);

        //$("###details_id##_from").val( $("###details_id##_interval_slider").slider("values", 0) );
        //$("###details_id##_to").val( $("###details_id##_interval_slider").slider("values", 1) );

        //$("#"+hData.name+this.intervalObjectIdPostfix).slider( "option" , "max", max );
        //$("#"+hData.name+this.intervalObjectIdPostfix).slider( "option" , "min", min );
        //$("#"+hData.name+this.intervalObjectIdPostfix).slider( "option" , "step" , step );
        //$("#"+hData.name+this.intervalObjectIdPostfix).slider( "values" , 0, selMin );
        //$("#"+hData.name+this.intervalObjectIdPostfix).slider( "values" , 1, selMax );
    },

    //
    // hData = {
    //    superInterval: {min: 0, max: 1},
    //    selectedVals: {min: 0, max: 1},
    //    existsVals: {min: 0, max: 1}
    //}
    //
    initInterval: function(name, hData) {

        //
        // Prepare values
        //
        var min = hData.superInterval.min * 1;
        var max = hData.superInterval.max * 1;
        var intValFrom = hData.selectedVals.min * 1;
        var intValTo = hData.selectedVals.max * 1;

        //console.log("intervalStepMode = "+this.intervalStepMode);
        switch(this.intervalStepMode){
            case "constant_static":
                hData.step = this.intervalStepConstant;
            case "constant_dynamic":
                hData.step = this.intervalStepConstant;
                var currentWidth = $("form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix).css("width") + "";
                //console.log("currentWidth = "+currentWidth);
                currentWidth = currentWidth.replace(/[^0-9]/g, "") * 1;
                //console.log("currentWidth = "+currentWidth);

                var steps = Math.ceil((max - min)/hData.step);
                if(steps+1 > currentWidth+1){
                    $("form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix).css("width", steps);
                }
                break;
            case "by_width":
                var currentWidth = $("form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix).css("width");
                //console.log("currentWidth = "+currentWidth);
                currentWidth = currentWidth.replace(/[^0-9]/g, "") * 1;
                //console.log("currentWidth = "+currentWidth);

                var range = Math.ceil(max - min);
                //console.log("range = "+range+", currentWidth = "+currentWidth);
                if(range > currentWidth){
                    hData.step = Math.ceil(range / currentWidth);
                    //console.log("scalc step"+hData.step);
                } else {
                    hData.step = 1;
                    //console.log("set minimal step "+hData.step);
                }
                break;
            case "default":
            default:
                break;
        }
        //intervalStepConstant: 100,
        //console.log("hData.step = "+hData.step);


        var valFrom = intValFrom;
        var valTo = intValTo;
        if(!uzAjaxFilter.showMinMaxIntervalValues){
            var valFrom = (intValFrom <= min) ? "" : intValFrom;
            var valTo = (intValTo >= max) ? "" : intValTo;
        }

        //
        // Destroy slider widget before reinit
        //
        if(typeof(this.hSlidersIsInit["form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix]) != "undefined"){
            $("form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix).slider( "destroy" );
        }

        //
        // Init slider widget
        //

        $("form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix).slider({
            range: true,
            step: hData.step * 1,
            min: min,
            max: max,
            values: [intValFrom, intValTo],
            slide: function( event, ui ) {
                //console.log("slide callback call oSlider.slider");

                var min = $(this).slider("option", "min");
                var max = $(this).slider("option", "max");

                var valFrom = ui.values[0];
                var valTo = ui.values[1];

                if(!uzAjaxFilter.showMinMaxIntervalValues){
                    if(valFrom <= min){
                        valFrom = "";
                    }
                    if(valTo >= max){
                        valTo = "";
                    }
                }
                //alert("min = "+min+", valFrom = "+valFrom);
                $("#" + uzAjaxFilter.filterFormName + "_" + name + "_from").val(valFrom);
                $("#" + uzAjaxFilter.filterFormName + "_" + name + "_to").val(valTo);
            },
            stop: function( event, ui ) {
                AMI.Message.send('uz_filter_slider_field_changed', $("#" + uzAjaxFilter.filterFormName + "_" + name + "_from"), $("#" + uzAjaxFilter.filterFormName + "_" + name + "_to"));
                AMI.Message.send('filter_field_changed', true);
            }
        });

        //console.log("init slider "+"form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix);
        this.hSlidersIsInit["form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix] = 1;

        //
        // Add different classes to left and right slidebars
        //
        $("form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix).find("a.ui-slider-handle").eq(0).addClass("uz-slide-handler-left");
        $("form[name=" + this.filterFormName + "] #"+name+this.intervalObjectIdPostfix).find("a.ui-slider-handle").eq(1).addClass("uz-slide-handler-right");

        //
        // Call custom apply slider functions
        //
        AMI.Message.send("on_slider_init", null, null);

        /* To replace left slider handle style:
        function uzCustomSlidersApply(){
            //$(".ui-slider-handle").addClass('ui-slider-handle-left');
            //$(".ui-slider-handle").css({background: 'url("/_img/arrow_slider_l.png") no-repeat scroll 0 0 transparent'});
            $(".ui-slider").each(function(){
                var aLinks = $(this).find(".ui-slider-handle");
                if(aLinks.length){
                    $(aLinks[0]).css({background: 'url("/_img/arrow_slider_l.png") no-repeat scroll 0 0 transparent'});
                }
            });
        }
        AMI.Message.addListener("on_slider_init", uzCustomSlidersApply);
        */




        //
        // Init inputs values
        //
        $("#" + this.filterFormName + "_" + name + "_from").val(valFrom);
        $("#" + this.filterFormName + "_" + name + "_to").val(valTo);
    },

    onMessageFilterFieldIntervalChange: function(fName, fPostfix){
        uzAjaxFilter.onFilterFieldIntervalChange(fName, fPostfix);
        return true;
    },

    onFilterFieldIntervalChange: function(fName, forcedPostfix){
        var oSlider = $("form[name=" + this.filterFormName + "] #"+fName+this.intervalObjectIdPostfix);
        //console.log("onFilterFieldIntervalChange call oSlider.slider");
        var min = oSlider.slider("option", "min");
        var max = oSlider.slider("option", "max");

        //console.log("min = "+min+", max = "+max);

        var valFrom = $("#" + this.filterFormName + "_"+fName+"_from").val();
        var valTo = $("#" + this.filterFormName + "_"+fName+"_to").val();

        //console.log("valFrom = "+valFrom);
        //console.log("valTo = "+valTo);

        var intValFrom = this.validateFloat(valFrom);
        //console.log("intValFrom = "+intValFrom);

        var intValTo = this.validateFloat(valTo);
        //console.log("intValTo = "+intValTo);

        // Fix min and max values if field changed before set real min and ax values
        if(typeof(min) == "object"){
            min = intValFrom;
        }
        if(typeof(max) == "object"){
            max = intValTo;
        }
        if(max < min){
            max = min;
        }
        //console.log("min = "+min+", max = "+max);


        if(!valFrom.length){
            intValFrom = min;
            forcedPostfix = '';
        }
        if(intValFrom <= min){
            intValFrom = min;
            if(uzAjaxFilter.showMinMaxIntervalValues){
                valFrom = min;
            } else {
                valFrom = '';
            }
            forcedPostfix = '';
        }

        if(!valTo.length){
            intValTo = max;
            forcedPostfix = '';
        }
        if(intValTo >= max){
            intValTo = max;
            if(uzAjaxFilter.showMinMaxIntervalValues){
                valTo = max;
            } else {
                valTo = '';
            }
            forcedPostfix = '';
        }

        //
        // Validate values
        //
        if(intValTo < intValFrom){
            switch(forcedPostfix){
                case "_from":
                    intValTo = intValFrom;
                    valTo = valFrom;
                    break;
                case "_to":
                    intValFrom = intValTo;
                    valFrom = valTo;
                    break;
                default:
                    // One of fields is empty, do not validate other field by forced
                    break;
            }
        }

        //console.log("after validate intValFrom = "+intValFrom+", intValTo = "+intValTo);

        // Fix Amiro bug with interval fields
        if(valFrom == "" && valTo != ""){
            valFrom = "0";
        }

        //
        // Set-back
        //
        //alert("set fields vals "+valFrom+", "+valTo);
        $("#" + this.filterFormName + "_"+fName+"_from").val(valFrom);
        $("#" + this.filterFormName + "_"+fName+"_to").val(valTo);

        //alert("set slider vals "+intValFrom+", "+intValTo);
        oSlider.slider("option", "values", [intValFrom, intValTo]);
        //console.log([intValFrom, intValTo]);

        AMI.Message.send('filter_field_changed', true);

    },


    escapeHtml: function(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    },














    setForcedSubmitUrl: function(catId) {
        /*
        var varName = this.oForm.name + "_forceSubmitUrl";
        if(document.getElementById(this.catSelectId)){
            var sublink = $("form[name=" + this.filterFormName + "] #"+this.catSelectId+" option:selected").attr("sublink")? $("form[name=" + this.filterFormName + "] #"+this.catSelectId+" option:selected").attr("sublink"): $("form[name=" + this.filterFormName + "] #"+this.catSelectId+" option:selected").prop("sublink");
            eval(varName + " = '" + this.scriptLink + "/" + sublink + "?'");
        }*/
    },

    // Return aray of fields used names with not empty value.
    getFieldsFilteredBy: function() {
        var aRes = new Array();
        if(typeof(this.aFields) == 'undefined'){
            return aRes;
        }
        if(this.oForm != false && this.aFields.length){
            for(var i = 0; i < this.aFields.length; i++){
                switch(this.aFields[i].type){
                    case "select":
                        if(this.aFields[i].multi == ''){
                            if(this.oForm.elements[this.aFields[i].name].value != ""){
                                aRes.push(this.aFields[i].name);
                            }
                        } else {
                            var oSel = this.oForm.elements[this.aFields[i]]
                            for(var j = 0; j < oSel.options.length; j++){
                                if(oSel.options[j].selected){
                                    aRes.push(this.aFields[i].name);
                                    break;
                                }
                            }
                        }
                        break;
                    case "checkbox":
                        var j = 0;
                        // TODO. Verify code
                        var cName = "chk_"+this.aFields[i].name+"_";
                        $("form[name=" + this.filterFormName + "] #" + cName + j + ":checked").each(function() {
                            if(this.val()) {
                                aRes.push(this.aFields[i].name);
                            }
                        });
                        break;
                    case "price_from":
                        if(this.oForm.elements[this.aFields[i].name].value != ""){
                            aRes.push(this.aFields[i].name);
                        }
                        break;
                    case "price_to":
                        if(this.oForm.elements[this.aFields[i].name].value != ""){
                            aRes.push(this.aFields[i].name);
                        }
                        break;
                    case "text":
                        if(this.oForm.elements[this.aFields[i].name].value != ""){
                            aRes.push(this.aFields[i].name);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return aRes;
    },

/*
    getFieldsDefaultDisabled: function() {
        if(this.oForm == null || this.aFields.length == 0){
            return;
        }
        for(var i = 0; i < this.aFields.length; i++){
            if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                switch(this.aFields[i].type){
                    case "select":
                        this.aFields[i].defaultDisabled = this.oForm[this.aFields[i].name].disabled;
                        //this.aDefaultFieldsDisabledStatus[this.aFields[i].name] = this.oForm[this.aFields[i].name].disabled;
                        break;
                    case "checkbox":
                        break;
                    case "price_from":
                        this.aFields[i].defaultDisabled = this.oForm[this.aFields[i].name].disabled;
                        this.aDefaultFieldsDisabledStatus[this.aFields[i].name] = this.oForm[this.aFields[i].name].disabled;
                        break;
                    case "price_to":
                        this.aDefaultFieldsDisabledStatus[this.aFields[i].name] = this.oForm[this.aFields[i].name].disabled;
                        break;
                    case "text":
                        this.aDefaultFieldsDisabledStatus[this.aFields[i].name] = this.oForm[this.aFields[i].name].disabled;
                        break;
                    default:
                        break;
                }
            }
        }
    },
//*/

    disableFields: function() {
        if(this.oForm == false || typeof(this.aFields) == 'undefined'){
            return;
        }
        for(var i = 0; i < this.aFields.length; i++){
            switch(this.aFields[i].type){
                case "select":
                    if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                        this.oForm[this.aFields[i].name].disabled = true;
                    }
                    break;
                case "checkbox":
                    /*
                    var j = 0;
                    var cName = "chk_"+this.aFields[i].name+"_";
                    while(document.getElementById(cName+j)){
                        if(document.getElementById(cName+j).checked && document.getElementById(cName+j).value != ""){
                            aRes.push(this.aFields[i].name);
                        }
                        j++;
                    }
                    //*/
                    break;
                case "radio":
                    /*
                    var j = 0;
                    var cName = "chk_"+this.aFields[i].name+"_";
                    while(document.getElementById(cName+j)){
                        if(document.getElementById(cName+j).checked && document.getElementById(cName+j).value != ""){
                            aRes.push(this.aFields[i].name);
                        }
                        j++;
                    }
                    //*/
                    break;
                case "price_from":
                    if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                        this.oForm[this.aFields[i].name].disabled = true;
                    }
                    break;
                case "price_to":
                    if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                        this.oForm[this.aFields[i].name].disabled = true;
                    }
                    break;
                case "text":
                    if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                        this.oForm[this.aFields[i].name].disabled = true;
                    }
                    break;
                case "interval":
                    //console.log("disableFields");
                    if(typeof(this.hSlidersIsInit["form[name=" + this.filterFormName + "] #"+this.aFields[i].name+this.intervalObjectIdPostfix]) != "undefined"){
                        //console.log(this.hSlidersIsInit);
                        //console.log("form[name=" + this.filterFormName + "] #"+this.aFields[i].name+this.intervalObjectIdPostfix);
                        //console.log("call oSlider.slider");
                        $("form[name=" + this.filterFormName + "] #"+this.aFields[i].name+this.intervalObjectIdPostfix).slider( "option" , "disabled", true );
                    }
                    break;
                default:
                    break;
            }
            // Disable subfields
            if(typeof(this.aFields[i].aSubfieldsId) != 'undefined'){
                this.setSubfieldsDisabledStatus(this.aFields[i].aSubfieldsId, true);
            }
        }

        AMI.Message.send("uz_on_filter_disable_fields", null, null);
    },

    enableFields: function() {
        if(this.oForm == false || typeof(this.aFields) == 'undefined'){
            return;
        }
        for(var i = 0; i < this.aFields.length; i++){
            switch(this.aFields[i].type){
                case "select":
                    //alert(this.toString[this.aFields]);
                    //this.oForm[this.aFields[i].name].disabled = this.aDefaultFieldsDisabledStatus[this.aFields[i].name];
                    if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                        this.oForm[this.aFields[i].name].disabled = false;
                    }
                    break;
                case "checkbox":
                    /*
                    var j = 0;
                    var cName = "chk_"+this.aFields[i].name+"_";
                    while(document.getElementById(cName+j)){
                        if(document.getElementById(cName+j).checked && document.getElementById(cName+j).value != ""){
                            aRes.push(this.aFields[i].name);
                        }
                        j++;
                    }
                    //*/
                    break;
                case "price_from":
                    //this.oForm[this.aFields[i].name].disabled = this.aDefaultFieldsDisabledStatus[this.aFields[i].name];
                    if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                        this.oForm[this.aFields[i].name].disabled = false;
                    }
                    break;
                case "price_to":
                    //this.oForm[this.aFields[i].name].disabled = this.aDefaultFieldsDisabledStatus[this.aFields[i].name];
                    if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                        this.oForm[this.aFields[i].name].disabled = false;
                    }
                    break;
                case "text":
                    //this.oForm[this.aFields[i].name].disabled = this.aDefaultFieldsDisabledStatus[this.aFields[i].name];
                    if(typeof(this.oForm[this.aFields[i].name]) != 'undefined'){
                        this.oForm[this.aFields[i].name].disabled = false;
                    }
                    break;
                case "interval":
                    //console.log("enableFields");
                    if(typeof(this.hSlidersIsInit["form[name=" + this.filterFormName + "] #"+this.aFields[i].name+this.intervalObjectIdPostfix]) != "undefined"){
                        //console.log("call oSlider.slider");
                        $("form[name=" + this.filterFormName + "] #"+this.aFields[i].name+this.intervalObjectIdPostfix).slider( "option" , "disabled", false );
                    }
                    break;
                default:
                    break;
            }

            // Enable subfields
            if(typeof(this.aFields[i].aSubfieldsId) != 'undefined'){
                this.setSubfieldsDisabledStatus(this.aFields[i].aSubfieldsId, false);
            }
        }

        AMI.Message.send("uz_on_filter_enable_fields", null, null);
    },

    setSubfieldsDisabledStatus: function(aSubfieldsId, status) {
        for(var i = 0; i < aSubfieldsId.length; i++){
            var subfieldId = aSubfieldsId[i];

            if(document.getElementById(subfieldId)){
                document.getElementById(subfieldId).disabled = status;
            }
        }
    },

    disableLabel: function(fieldId) {
        $("form[name=" + this.filterFormName + "] #"+fieldId+"_label").addClass(this.disabledClass);
        $("form[name=" + this.filterFormName + "] #"+fieldId+"_div").addClass(this.disabledClass2);
        //alert("disableLabel "+"#"+fieldId+"_label");
    },

    enableLabel: function(fieldId) {
        $("form[name=" + this.filterFormName + "] #"+fieldId+"_label").removeClass(this.disabledClass);
        $("form[name=" + this.filterFormName + "] #"+fieldId+"_div").removeClass(this.disabledClass2);
        //alert("enableLabel "+"#"+fieldId+"_label");
    },


    updateLabelQty: function(fieldId, qty) {
        var hData = {qty: qty};
        var res = AMI.Template.parse(this.fldValsQtyTpl, hData);
        $("form[name=" + this.filterFormName + "] #"+fieldId+"_label_qty").html(res);
        //alert("enableLabel "+"#"+fieldId+"_label");
    },



    //
    // Apply filted data section end
    //








    //
    // Select category section
    //
    /*
    initCurrentCat: function(){
        if(document.getElementById(this.catSelectId) && typeof(this.oForm.elements["catid"]) != 'undefined'){
            this.currentCatId = this.oForm.elements["catid"].value;
            document.getElementById(this.catSelectId).value = this.currentCatId;
        }
    },
    //*/
    //
    // Select category section end





    //





    //
    // Form interactive widgets section
    //

    onMessageFilterFieldCaptionClick: function(fldName, p2){
        uzAjaxFilter.onFilterFieldCaptionClick(fldName);
        return true;
    },

    onFilterFieldCaptionClick: function(fldName){
        $("form[name=" + this.filterFormName + "] #"+fldName+this.fieldValuesBoxIdPostfix).toggleClass(this.hideValuesClass);
    },

    onLoadShowFieldsValues: function(){
        if(this.oForm != null && this.aFields.length){
            var i, j, val, show;
            //alert(this.toString(this.aFields));
            for(i = 0; i < this.aFields.length; i++){
                val = this.getFieldValue(this.aFields[i].formName);
				if(val.length>=1 && this.aFields[i].type!='interval' && val[0]!=''){
					$("form[name=" + this.filterFormName + "] #"+this.aFields[i].name+this.fieldValuesBoxIdPostfix).prev().toggleClass('opened').toggleClass('closed').next().show();//.toggleClass(this.hideValuesClass);
				} else
				if(this.aFields[i].type=='interval'&& val[0]!='' && val[1]!=''&&(val[0]!='0' || val[1]!='0') ){
					$("form[name=" + this.filterFormName + "] #"+this.aFields[i].name+this.fieldValuesBoxIdPostfix).show().parent().children('div').eq(0).toggleClass('slp_hd').toggleClass('slp_sh');
				}	
                show = false;
                if(!this.forceFieldsMinimize){
                    switch(this.aFields[i].type){
                        //select|checkbox|radio|text|interval|val_from|val_to
                        case "checkbox":
                            //alert(this.aFields[i].formName+" = "+this.toString(val));
                            //alert(this.aFields[i].formName);
                            if(this.aFields[i].formName.indexOf("[]") < 0){
                                show = true;
                            } else {
                                // Multiple checkboxes
                                //alert("Multiple checkboxes");
                                if(val.length){
                                    //for(j in val){
                                    for(j = 0;  j < val.length; j++){
                                        if(val[j] != this.aFields[i].emptyValue){
                                            //alert(val[j]+" != "+this.aFields[i].emptyValue+" is true");
                                            show = true;
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        case "radio":
                            if(val != this.aFields[i].emptyValue){
                                show = true;
                            }
                            break;
                        case "select":
                            if(this.aFields[i].multi == ''){
                                show = true;
                            } else {
                                // Multiple checkboxes
                                if(val.length){
                                    //for(j in val){
                                    for(j = 0;  j < val.length; j++){
                                        if(val[j] != this.aFields[i].emptyValue){
                                            show = true;
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        case "interval":
                            show = true;
                            break;
                        default:
                            if(val != this.aFields[i].emptyValue){
                                show = true;
                            }
                            break;
                    }
                }

                if(show){
                    //alert("show field vals "+this.aFields[i].name);

                    // Show values box
                    $("form[name=" + this.filterFormName + "] #"+this.aFields[i].name+this.fieldValuesBoxIdPostfix).removeClass(this.hideValuesClass);

                    // Force hide values in caption
                    this.showSelectedValuesIntoCaption(this.aFields[i], false);
                } else {
                    // Show values in caption
                    if(this.showSelectedValsWhenFieldIsMinimized){
                        this.showSelectedValuesIntoCaption(this.aFields[i], true);
                    }
                    //alert("leave hidden field vals "+this.aFields[i].name);
                }
            }
        }
    },


    showSelectedValuesIntoCaption: function(hFieldData, showMode){
        var res = "";
        if(showMode){
            res = this.getFieldValue(hFieldData.formName);
            //alert("res = "+this.toString(res));
            //console.debug("res = "+this.toString(res)+", type = "+typeof(res));
            switch(typeof(res)){
                case "string":
                    break;
                case "array":
                case "object":
                    if(res.length > this.maxValsToShowWhenFieldIsMinimized){
                        var qty = res.length;
                        if(qty == 1){
                            if(res[0] == ""){
                                qty = 0;
                            }
                        }
                        res = "";
                        if(qty){
                            var hData = {qty: qty};
                            res = AMI.Template.parse(this.fldCaptionQtyTpl, hData);
                        }
                    } else {
                        res = res.join(", ");
                    }
                    break;
                default:
                    break;
            }
        }
        $("form[name=" + this.filterFormName + "] #"+hFieldData.name+this.fieldCaptionValuesBoxIdPostfix).html(res);

        // Custom code:
        /*
        if(res) {
            if(res[0] != ',') {
                document.getElementById('leg').style.display='none';
                document.getElementById('td_capt').style.width='100%';
            }
        }*/
    },



    onMessageFilterFieldCaptionDescClick: function(fldName, oObj){
        uzAjaxFilter.onFilterFieldCaptionDescClick(fldName,oObj);

        return true;
    },

    onFilterFieldCaptionDescClick: function(fldName,oObj){
       // alert('show dialog');

        var dlgheight = $('.dialog').height();
        var left_offset_position = $(oObj).position().left;
        var top_offset_position = $(oObj).position().top+dlgheight+10;

        $('.dialog').css("top",top_offset_position+'px');
        $('.dialog').css("left",left_offset_position+'px');
        $('.dialog').show();
        $(document).mousedown( function(event){
            if( $(event.target).closest(".dialog").length ){
                return;
            }
            $(".dialog").fadeOut("slow");
            $(document).unbind('mousedown');
            event.stopPropagation();
        });
    },


    //
    // Form interactive section
    //




















    //
    // Stuff functions
    //



    toJsonString: function (mixed_val) {
        var retVal, json = window.JSON;
        try {
            if (typeof json === 'object' && typeof json.stringify === 'function') {
                retVal = json.stringify(mixed_val); // Errors will not be caught here if our own equivalent to resource
                //  (an instance of PHPJS_Resource) is used
                if (retVal === undefined) {
                    throw new SyntaxError('json_encode');
                }
                return retVal;
            }

            var value = mixed_val;

            var quote = function (string) {
                var escapable = /[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
                var meta = { // table of character substitutions
                    '\b': '\\b',
                    '\t': '\\t',
                    '\n': '\\n',
                    '\f': '\\f',
                    '\r': '\\r',
                    '"': '\\"',
                    '\\': '\\\\'
                };

                escapable.lastIndex = 0;
                return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
                    var c = meta[a];
                    return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                }) + '"' : '"' + string + '"';
            };

            var str = function (key, holder) {
                var gap = '';
                var indent = '    ';
                var i = 0; // The loop counter.
                var k = ''; // The member key.
                var v = ''; // The member value.
                var length = 0;
                var mind = gap;
                var partial = [];
                var value = holder[key];

                // If the value has a toJSON method, call it to obtain a replacement value.
                if (value && typeof value === 'object' && typeof value.toJSON === 'function') {
                    value = value.toJSON(key);
                }

                // What happens next depends on the value's type.
                switch (typeof value) {
                case 'string':
                    return quote(value);

                case 'number':
                    // JSON numbers must be finite. Encode non-finite numbers as null.
                    return isFinite(value) ? String(value) : 'null';

                case 'boolean':
                case 'null':
                    // If the value is a boolean or null, convert it to a string. Note:
                    // typeof null does not produce 'null'. The case is included here in
                    // the remote chance that this gets fixed someday.
                    return String(value);

                case 'object':
                    // If the type is 'object', we might be dealing with an object or an array or
                    // null.
                    // Due to a specification blunder in ECMAScript, typeof null is 'object',
                    // so watch out for that case.
                    if (!value) {
                        return 'null';
                    }
                    if ((this.PHPJS_Resource && value instanceof this.PHPJS_Resource) || (window.PHPJS_Resource && value instanceof window.PHPJS_Resource)) {
                        throw new SyntaxError('json_encode');
                    }

                    // Make an array to hold the partial results of stringifying this object value.
                    gap += indent;
                    partial = [];

                    // Is the value an array?
                    if (Object.prototype.toString.apply(value) === '[object Array]') {
                        // The value is an array. Stringify every element. Use null as a placeholder
                        // for non-JSON values.
                        length = value.length;
                        for (i = 0; i < length; i += 1) {
                            partial[i] = str(i, value) || 'null';
                        }

                        // Join all of the elements together, separated with commas, and wrap them in
                        // brackets.
                        v = partial.length === 0 ? '[]' : gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' : '[' + partial.join(',') + ']';
                        gap = mind;
                        return v;
                    }

                    // Iterate through all of the keys in the object.
                    for (k in value) {
                        if (Object.hasOwnProperty.call(value, k)) {
                            v = str(k, value);
                            if (v) {
                                partial.push(quote(k) + (gap ? ': ' : ':') + v);
                            }
                        }
                    }

                    // Join all of the member texts together, separated with commas,
                    // and wrap them in braces.
                    v = partial.length === 0 ? '{}' : gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' : '{' + partial.join(',') + '}';
                    gap = mind;
                    return v;
                case 'undefined':
                    // Fall-through
                case 'function':
                    // Fall-through
                default:
                    throw new SyntaxError('json_encode');
                }
            };

            // Make a fake root object containing our value under the key of ''.
            // Return the result of stringifying the value.
            return str('', {
                '': value
            });

        } catch (err) { // Todo: ensure error handling above throws a SyntaxError in all cases where it could
            // (i.e., when the JSON global is not available and there is an error)
            if (!(err instanceof SyntaxError)) {
                throw new Error('Unexpected error type in json_encode()');
            }
            this.php_js = this.php_js || {};
            this.php_js.last_error_json = 4; // usable by json_last_error()
            return null;
        }
    },


    inArray: function(val, aVals){
        var res = 0;
        var i;
        if(typeof(aVals) == 'object'){
            // Check value in object
            for(i in aVals){
                if(aVals[i] == val){
                    res = 1;
                    break;
                }
            }
        } else {
            // Expects aVals is an array
            if(aVals.length){
                for(var i = 0; i < aVals.length; i++){
                    if(aVals[i] == val){
                        res = 1;
                        break;
                    }
                }
            }
        }
        return res;
    },

    isObjEmpty: function(aVals){
        var res = 0;
        var i, cnt;
        switch(typeof(aVals)){
            case "object":
                // Check values in object
                cnt = 0;
                for(i in aVals){
                    cnt++;
                }
                if(cnt == 0){
                    res = 1;
                }
                break;
            default:
                // For arrays
                if(typeof(aVals.length) != 'undefined'){
                    if(aVals.length == 0){
                        res = 1;
                    }
                }
                break;
        }
        return res;
    },

    // Debug object to string
    toString: function(oData, indent){
        if(typeof(indent) == 'undefined'){
            var indent = "";
        }
        var res = "";
        if(typeof(oData) == 'object'){
            for(var i in oData){
                if(typeof(oData[i]) == 'object'){
                  res += indent + i+": {\n"+this.toString(oData[i], indent + "  ")+indent + "}\n";
                } else {
                    if(typeof(oData[i]) == 'function'){
                          res += indent + i+":function\n";
                    } else {
                          res += indent + i+":"+oData[i]+"\n";
                    }
                }
            }
        } else {
            res += indent + oData+"\n";
        }
        return res;
    },

    cloneObject: function (obj){
        if(obj == null || typeof(obj) != 'object')
            return obj;
        var temp = new obj.constructor();
        for(var key in obj)
            temp[key] = this.cloneObject(obj[key]);
        return temp;
    },

    validateFloat: function(val, roundPrecision){
        //console.log("val = "+val);
        val = val+"";
        val = val.replace(",", ".");
        val = val.replace(" ", "");

        var re = /^[-+]{0,1}[0-9]+[.]{0,1}[0-9]*$/;
        if(re.test(val)){
            //console.log(val+" is number");
            val = val * 1;
        } else {
            //console.log(val+" is not number");
            val = 0;
        }
        //console.log("prep val = "+val);

        /*
        if(isNaN(val) || val == ""){
            val = 0;
        }

        val = parseFloat(val);
        */

        if(val < 0 ){
            val = 0;
        }

        if(typeof(roundPrecision) == 'undefined'){
            var roundPrecision = this.roundPrec;
        }
        val = this.round(val, roundPrecision);
        //console.log("res = "+val);
        return val;
    },

    validateInt: function(val){
        if(isNaN(val) || val == ""){
            val = 0;
        }
        val = parseInt(val);
        if(val < 0 ){
            val = 0;
        }
        return val;
    },

    round: function(val, prec){
        if(typeof(prec) == 'undefined'){
            prec = this.roundPrec;
        }
        //alert(val);
        var rounder = Math.pow(10, prec);
        val = Math.round(val * rounder);
        val = val / rounder;
        //alert(val);
        return val;
    },

    addToDebug: function(str, replace){
        //str = "<pre>"+str+"</pre>";
        if(typeof(replace) == 'undefined'){
            var replace = false;
        }
        if(replace){
            $("form[name=" + this.filterFormName + "] #"+this.debugBox).html(str);
        } else {
            $("form[name=" + this.filterFormName + "] #"+this.debugBox).append(str);
        }
    },

    checkMinVer: function(ver){
        return ([$.fn.jquery, ver].sort()[0] == ver);
    },


    endvar: 1
}