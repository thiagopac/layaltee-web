var FormControls = {
    init: function() {
        $("#m_form_1").validate({
            rules: {
                email: {
                    required: !0,
                    email: !0,
                    minlength: 10
                },

                digits: {
                    required: !0,
                    digits: !0
                },

                phone: {
                    required: !0,
                    phoneUS: !0
                },

                checkbox: {
                    required: !0
                },

                checkboxes: {
                    required: !0,
                    minlength: 1,
                    maxlength: 2
                },

                radio: {
                    required: !0
                }
            },
            invalidHandler: function(e, r) {
                var i = $("#m_form_1_msg");
                i.removeClass("m--hide").show(), mApp.scrollTo(i, -200)
            },
            submitHandler: function(e) {}
        }), $("#m_form_2").validate({
            rules: {
                email: {
                    required: !0,
                    email: !0
                },

                digits: {
                    required: !0,
                    digits: !0
                },

                phone: {
                    required: !0,
                    phoneUS: !0
                },

                checkbox: {
                    required: !0
                },
                checkboxes: {
                    required: !0,
                    minlength: 1,
                    maxlength: 2
                },
                radio: {
                    required: !0
                }
            }
        })
    }
};
jQuery(document).ready(function() {
    FormControls.init()
});
