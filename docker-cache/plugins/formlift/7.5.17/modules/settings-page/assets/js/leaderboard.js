var FormLiftLeaderBoard = {
  getPostition: function () {
    var ajaxCall = jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { action: 'formlift_get_leaderboard_ranking' },
      success: function (ranking) {
        var wrapper = document.createElement('div')
        wrapper.innerHTML = ranking
        var rank = wrapper.firstChild
        jQuery('.formlift-rank').html(rank)
      }
    })
  }
}

jQuery(document).ready(function () {
  jQuery('.get-rank').on('click', FormLiftLeaderBoard.getPostition)
})