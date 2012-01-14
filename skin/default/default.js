var m_submited = false;
var m_waitMessage = null;
var dir = -1;

function step(amount)
{
	var index = Math.round(amount);
	var str = "";
	for(var i = 0; i <= 20; i++)
	{
		str += i == index ? "+" : "-";
	}
	m_waitMessage.text(str);
}

function animation()
{
	m_waitMessage.animate(
	{
		marginRight: (dir *= -1) > 0 ? "20px" : "0px",
	},
	{
		duration: 2000,
		easing: "swing",
		step: step,
		complete: animation,
	});
}

function onSubmit(anime)
{
	var submited = m_submited;
	if(!submited)
	{
		m_submited = true;
		var btns = $('.submit')
		btns.css(
		{
			backgroundColor: "#999999",
			cursor: "wait",
		});
		m_waitMessage = $("<br /><em>学習中です。お待ちください......</em>");
		btns.parent().append(m_waitMessage);
		if(anime)
		{
			animation();
		}
	}
	return !submited;
}
