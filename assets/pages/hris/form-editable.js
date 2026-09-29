var FormEditable = function () {



    PECO.getSelect2Plugins();
    PECO.getNumberFormatPlugin();

    var init_input_editable;
    var filedropzone = $(document).find('#accompfiledrop');




    var init_profile = function () {




        filedropzone.on('filebatchuploadsuccess', function(event, data, previewId, index) {
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            PECO.initAlerts(response.msg, 'Upload File', 'error', false, false);
            filedropzone.fileinput('clear');
        });


        PECO.select2Basic($('#gendercombo') , 'hris/getgender' , 'Select gender' , false, false,false);

        $(document).on('submit','#uploadform',function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url:PECO.base_url()+"hris/uploadprofilepic",
                type:"post",
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                dataType:"json"
            }).done(function (d) {
                PECO.initAlerts(d.msg , "PECO.net" , d.func);
            });
        });

    };

    init_input_editable = function () {
        //$.fn.editable.defaults.mode = 'inline';

        $('#firstname').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter firstname',
            inputclass: 'form-control input-medium',
            placeholder: 'Firstname',
            showbuttons: false
        });

        $('#lastname').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter lastname',
            inputclass: 'form-control input-medium',
            placeholder: 'Lastname',
            showbuttons: false
        });

        $('#middlename').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter middlename',
            inputclass: 'form-control input-medium',
            placeholder: 'Middlename',
            showbuttons: false
        });

        $('#contactnumber').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Contact Number',
            inputclass: 'form-control input-medium',
            placeholder: 'Contact Number'
        });

        $('#gender').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Gender',
            inputclass: 'form-control input-medium',
            placeholder: 'Gender',
            limit: 1,
            source: [
                {value: 1, text: 'Male'},
                {value: 2, text: 'Female'},
            ]
        });

        $('#status').editable({
            success: function (response, newValue) {

                console.log('RES: ' + response);
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;

                }
            },

            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/marital',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                }
            },

            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Marital Status',
            inputclass: 'form-control input-medium',
            placeholder: 'Marital Status'
        });

        $('#gender').on('shown', function (e, reason) {
            PECO.initUniform();
        });

        $('#birthdate').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Birthday',
            inputclass: '',
            placeholder: 'Birthday',
            placement: 'bottom'
        });

        $('#bioid').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Biometric ID',
            inputclass: 'form-control',
            placeholder: 'Biometric ID',
            placement: 'right'
        });

        $('#employeefirstname').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Firstname Name',
            emptytext: 'Enter Firstname Name',
            placeholder: 'Employee Firstname Name',
            placement: 'bottom',
            inputclass: 'form-control input-small'
        });
        $('#employeemiddlename').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Middle Name',
            emptytext: 'Enter Middle Name',
            placeholder: 'Employee Middle Name',
            placement: 'bottom',
            inputclass: 'form-control input-small'
        });

        $('#employeelastname').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Employee Last Name',
            emptytext: 'Enter Employee Last Name',
            placeholder: 'Employee Last Name',
            placement: 'bottom',
            inputclass: 'form-control input-small'
        });

        $('#addrspec').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Address',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Specific Address',            
            placeholder: 'Address'
        });


        $('#homephone').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Home Phone',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Home Phone',
            placeholder: 'Home Phone'
        });

        $('#workphone').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Work Phone',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Work Phone',
            placeholder: 'Work Phone'
        });
        $('#cellphone').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Cell Phone',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Cell Phone',
            placeholder: 'Cell Phone'
        });
        $('#emailaddress').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Email Address',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Email Address',
            placeholder: 'Email Address'
        });
        $('#companyemail').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Company Email',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Company Email',
            placeholder: 'Company Email'
        });
        $('#height').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Height',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Height',
            placeholder: 'Height'
        });
        $('#weight').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Weight',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Weight',
            placeholder: 'Weight'
        });
        $('#placeofbirth').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Place of Birth',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Place of Birth',
            placeholder: 'Place of Birth'
        });




        $('#city').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2city',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify City',
            placeholder: 'Modify City',
            inputclass: 'form-control input-large',
            emptytext: 'Select City',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });


        $('#country').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '100%',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2country',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                    icon: item.flag,
                                };
                            })
                        };
                    }
                },
                formatResult: PECO.formatDataListCountry, // omitted for brevity, see the source of this page
                formatSelection: PECO.formatDataSelectionCountry, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Country',
            placeholder: 'Modify Country',
            inputclass: 'form-control input-large',
            emptytext: 'Select Country',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });

        $('#nationality').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '100%',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2nationality',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                    icon: item.flag,
                                };
                            })
                        };
                    }
                },
                formatResult: PECO.formatDataListCountry, // omitted for brevity, see the source of this page
                formatSelection: PECO.formatDataSelectionCountry, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Nationality',
            placeholder: 'Modify Nationality',
            inputclass: 'form-control input-large',
            emptytext: 'Select Nationality',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });






        $('#pencil').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            $('#note').editable('toggle');

            // NOT WORKING
            $('#note').editable({
                url: PECO.base_url() + 'hris/editinfo',
                title: 'Enter Job Descriptions',
                inputclass: 'form-control input-medium',
                placeholder: 'Job Descriptions',
            });
        });


        //start sss editable
        $('#sss').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify SSS',
            inputclass: '',
            emptytext: 'Enter SSS',
            placeholder: 'SSS',
        });
        //end for editable sss

        //start for tin
        $('#tin').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Tin Number',
            inputclass: '',
            placeholder: 'Tin Number',
        });
        //end for tin
        //start for pag ibig
        $('#pagibig').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Pagibig Number',
            inputclass: '',
            placeholder: 'Pagibig Number',
        });
        //end for pag ibig
        //start for philhealth
        $('#philhealth').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Philhealth Number',
            inputclass: '',
            placeholder: 'Philhealth Number',
        });
        //end for philihealth
        // start for passport
        $('#passport').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Passport Number',
            inputclass: '',
            placeholder: 'Passport Number',
        });
        //end for passport
        //start for driver license number
        $('#driver').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Driver\'s License Number',
            inputclass: '',
            placeholder: 'Driver\'s License Number',
        });
        //end for driver license number
        //start for driver exp
        $('#driverexp').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Driver\'s License Expiry',
            inputclass: '',
            placeholder: 'Driver\'s License Expiry',
        });
        //end for driver expiry
        //start for bank name
        $('#bank').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Bank Name',
            inputclass: '',
            placeholder: 'Bank Name',
            emptytext: 'Bank Name.',
        });
        //end for bank name
        //start for bank id
        $('#bankid').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Bank Number',
            inputclass: '',
            placeholder: 'Bank Number',
            type:'input', tpl: '<input maxlength="11"></input>',
            emptytext: 'Account No.',
        });

        $('#salary').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Salary',
            inputclass: '',
            placeholder: 'Salary',
        });
        // end for bank id
        //start for other id
        $('#other').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter OtherID name',
            inputclass: '',
            placeholder: 'OtherID name',
        });
        //end for other id
        //start for other id id
        $('#otherid').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Other ID Number',
            inputclass: '',
            placeholder: 'Other ID Number',
        });
        // end for otheridid

        //start for editable date started
        $('#datestart').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Date Started',
            inputclass: '',
            placeholder: 'Date Started',
            placement: 'bottom',

        });
         
     
        
         $('#emp_salary').editable({
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Enter Employee Salary',
            inputclass: '',
            placeholder: 'Salary',

        });

// start test select2 for payclass
        $('#payclass').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2payclass',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Pay Class',
            placeholder: 'Modify Pay Class',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Pay Class',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });
// end test select for payclass

// start for employee job cate gory


        $('#jobcat').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2jobcat',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Status',
            placeholder: 'Modify Status',
            inputclass: 'form-control',
            emptytext: 'Enter Status',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });


        $('#agency').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2agency',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Agency',
            placeholder: 'Modify Agency',
            inputclass: 'form-control',
            emptytext: 'Enter Agency',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });

// end for employeee job category

// start for employee position code
        $('#empost').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2position',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Position',
            placeholder: 'Modify Position',
            inputclass: 'form-control',
            emptytext: 'Enter Position',
            placement: 'bottom'
        }).on('click', function () {
            PECO.select2_scroller();
        });


        $('#position').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2position',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Position',
            placeholder: 'Modify Position',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Position',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });
        // end for employee position code

        //new editable for district
        $('#district').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2district',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify District',
            placeholder: 'Modify District',
            inputclass: 'form-control input-large',
            emptytext: 'Enter District',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });

        $('#civilstatus').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2civilstatus',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Civil Status',
            placeholder: 'Modify Civil Status',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Civil Status',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });

        $('#bloodtype').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2bloodtype',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Blood Type',
            placeholder: 'Modify Blood Type',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Blood Type',
            placement: 'bottom'
        }).on('click', function () {
            PECO.select2_scroller();
        });

        $('#religion').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2religion',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Religion',
            placeholder: 'Modify Religion',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Religion',
            placement: 'bottom'
        }).on('click', function () {
            PECO.select2_scroller();
        });


        $('#educattainment').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2educationalattainment',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Educational Attainment',
            placeholder: 'Modify Educational Attainment',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Educational Attainment',
            placement: 'bottom'
        }).on('click', function () {
            PECO.select2_scroller();
        });

        $('#license').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2titlelicense',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify License',
            placeholder: 'Modify License',
            inputclass: 'form-control input-large',
            emptytext: 'Enter License',
            placement: 'bottom'
        }).on('click', function () {
            PECO.select2_scroller();
        });

        //start for employee department

        $('#department').editable({
            success: function (response, newValue) {
                if (!response.success)
                    return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/select2department',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
                //formatResult: PECO.formatState, // omitted for brevity, see the source of this page
                // formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            },
            url: PECO.base_url() + 'hris/editinfo',
            title: 'Modify Department',
            placeholder: 'Modify Department',
            inputclass: 'form-control input-large',
            emptytext: 'Enter Department',
            placement: 'bottom',
        }).on('click', function () {
            PECO.select2_scroller();
        });

    };

    return {
        //main function to initiate the module
        init: function () {
            init_profile();
            init_input_editable();

        }

    };

}();
