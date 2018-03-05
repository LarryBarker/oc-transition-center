/**
 * @license MIT 
 *
 * Creato by Webz Ray
 */
CKEDITOR.dialog.add( 'wenzgmapDialog', function( editor ) {

    return {
        title: 'Insert Google Map',
        minWidth: 400,
        minHeight: 75,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'addressHome',
                        label: 'Please verify your home address:',
                        default: '401 S. Emporia Wichita, KS 67202',
                    },
                    {
                        type: 'text',
                        id: 'destinationAddress',
                        label: 'Please enter the destination address:',
                    }
                    /*{
                        type: 'text',
                        id: 'mapWidth',
                        label: 'Map Width (px)',
                        style:'width:25%;',
                    },
                    {
                        type: 'text',
                        id: 'mapHeight',
                        label: 'Map Height (px)',
                        style: 'width:25%;',
                    }*/
                ]
            }
        ],
        onOk: function() {
            var dialog = this;
            var apiKey = 'AIzaSyABAqX5wdwAAmaIfVbf0f7zX3I5E0Uh9S0';
            var addressHome = dialog.getValueOf('tab-basic', 'addressHome').trim();
            var destinationAddress = dialog.getValueOf('tab-basic', 'destinationAddress').trim();
            var mapWidth = '600';
            var mapHeight = '600';
            //var mapWidth = dialog.getValueOf('tab-basic', 'mapWidth').trim();
            //var mapHeight = dialog.getValueOf('tab-basic', 'mapHeight').trim();
			/*var regExURL=/v=([^&$]+)/i;
			var id_video=url.match(regExURL);
			
			if(id_video==null || id_video=='' || id_video[0]=='' || id_video[1]=='')
				{
				alert("URL invalid! Try a sample like a\n\n\t http://www.youtube.com/watch?v=abcdef \n\n Thank you!");
				return false;
				}
            */
            var oTag = editor.document.createElement( 'iframe' );
			
            oTag.setAttribute('width', mapWidth);
            oTag.setAttribute('height', mapHeight);
            //oTag.setAttribute('src', '//maps.google.com/maps?q=' + url + '&num=1&t=m&ie=UTF8&z=14&output=embed');

            oTag.setAttribute('src', 'https://www.google.com/maps/embed/v1/directions?key=' + apiKey + '&origin=' + addressHome + '&destination=' + destinationAddress + '&mode=transit');

			oTag.setAttribute( 'frameborder', '0' );
			oTag.setAttribute('scrolling', 'no');

            editor.insertElement( oTag );
        }
    };
});