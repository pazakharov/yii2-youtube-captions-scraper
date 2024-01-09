<?php

namespace Zakharov;

use yii\base\Component;

class YoutubeScreaper extends Component
{
    /**
     * @param string $videoId
     *
     * @return string
     */
    public function getCaptionsBaseUrl(string $videoId)
    {
        return 'https://www.youtube.com/api/timedtext?v=' . $videoId;
    }
}
