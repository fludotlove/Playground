Basic Usage
===========

Add an anchor to your mark-up.

    <a id="ticker" href="#"></a>

Pass a JSON object to the News Reel.

    $('#ticker').newsReel({
        0: {
            'title': 'Wozniak: I wish Apple & Google were partners',
            'link': 'http://www.bbc.co.uk/news/technology-24758791'
        },
        1: {
            'title': 'Call of Duty: Ghosts launches ahead of new games consoles',
            'link': 'http://www.bbc.co.uk/news/technology-24803894'
        },
        2: {
            'title': 'Bitcoin at risk of network attack, say researchers',
            'link': 'http://www.bbc.co.uk/news/technology-24818975'
        },
        3: {
            'title': 'Grand Theft Auto: One of Britain\'s finest cultural exports?',
            'link': 'http://www.bbc.co.uk/news/technology-24066068'
        }
    });

Check code for some other settings/options.