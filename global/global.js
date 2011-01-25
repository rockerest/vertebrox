function go_here(loc, delay_ms)
{
	window.location=loc;
	return false;
}

function getUrlVars()
{
	var map = {};
/* <![CDATA[ */
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
/* ]]> */
		function(m,key,value)
		{
      		map[key] = value;
      	}
	);
    return map;
}

function insertParam(key, value, retain)
{
    key = escape(key); value = escape(value);

    var kvp = document.location.search.substr(1).split('&');
	if( retain != null && retain == false )
	{
		kvp = "";
	}
	if (kvp == "")
	{
		document.location.search = key + "=" + value;
    }
	else
	{
		var i=kvp.length; var x; while(i--) 
		{
			x = kvp[i].split('=');

			if (x[0]==key)
			{
					x[1] = value;
					kvp[i] = x.join('=');
					break;
			}
		}

		if(i<0) {kvp[kvp.length] = [key,value].join('=');}

		//this will reload the page, it's likely better to store this until finished
		document.location.search = kvp.join('&');
	}
}
