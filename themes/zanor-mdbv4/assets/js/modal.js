jQuery(function(){
    $('#userAgreement').modal({
        show:true,
        backdrop:'static',
        keyboard:false
    });
    $("#acceptAgreement").click(function(){
        $("#userAgreement").modal('hide');
        $("#questionnaire").modal({
            show: true,
            backdrop:'static',
            keyboard:false
        });
    });
    $("#submitQuestionnaire").click(function(){
        $("#questionnaire").modal('hide');
    })
});