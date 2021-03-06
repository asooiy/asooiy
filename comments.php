<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['theme_url'] = $this->options->themeUrl;
$header = "<script type=\"text/javascript\">
(function () {
    window.TypechoComment = {
        dom : function (id) {
            return document.getElementById(id);
        },

        create : function (tag, attr) {
            var el = document.createElement(tag);

            for (var key in attr) {
                el.setAttribute(key, attr[key]);
            }

            return el;
        },
        reply : function (cid, coid) {
            var comment = this.dom(cid), parent = comment.parentNode,
                response = this.dom('" . $this->respondId . "'), input = this.dom('comment-parent'),
                form = 'form' == response.tagName ? response : response.getElementsByTagName('form')[0],
                textarea = response.getElementsByTagName('textarea')[0];
            if (null == input) {
                input = this.create('input', {
                    'type' : 'hidden',
                    'name' : 'parent',
                    'id'   : 'comment-parent'
                });
                form.appendChild(input);
            }
            input.setAttribute('value', coid);
            if (null == this.dom('comment-form-place-holder')) {
                var holder = this.create('div', {
                    'id' : 'comment-form-place-holder'
                });
                response.parentNode.insertBefore(holder, response);
            }
            comment.appendChild(response);
            this.dom('cancel-comment-reply-link').style.display = '';
            if (null != textarea && 'text' == textarea.name) {
                textarea.focus();
            }
            return false;
        },
        cancelReply : function () {
            var response = this.dom('{$this->respondId}'),
            holder = this.dom('comment-form-place-holder'), input = this.dom('comment-parent');
            if (null != input) {
                input.parentNode.removeChild(input);
            }
            if (null == holder) {
                return true;
            }
            this.dom('cancel-comment-reply-link').style.display = 'none';
            holder.parentNode.insertBefore(response, holder);
            return false;
        }
    };
})();
</script>
";
echo $header;
?>


<?php
function threadedComments($comments, $options) {
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';
        } else {
            $commentClass .= ' comment-by-user';
        }
    }

    $commentLevelClass = $comments->levels > 0 ? ' comment-child' : ' comment-parent';
?>

<li id="li-<?php $comments->theId(); ?>" class="comment-body<?php
if ($comments->levels > 0) {
    echo ' comment-child';
    $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
} else {
    echo ' comment-parent';
}
$comments->alt(' comment-odd', ' comment-even');
echo $commentClass;
?>">

    <div id="<?php $comments->theId(); ?>">
        <div class="comment-inner">
            <div class="comment-author">
                <?php $comments->gravatar('40', ''); ?>
                <span><?php $comments->author(); ?></span>
            </div>
            <div class="comment-meta">
                <span><?php $comments->date('Y-m-d H:i'); ?></span>
            </div>
            <div class="comment-content">
              <?php
                $cos = preg_replace('#\@\((.*?)\)#','<img src="'.$GLOBALS['theme_url'].'/IMG/bq/$1.png" class="bq">',$comments->content);
                echo $cos;
              ?>
            </div>
            <span class="comment-reply"><?php $comments->reply(); ?></span>
        </div>
    </div>
<?php if ($comments->children) { ?>
    <div class="comment-children">
        <?php $comments->threadedComments($options); ?>
    </div>
<?php } ?>
</li>
<?php } ?>


<div id="comments">
    <?php $this->comments()->to($comments); ?>
    <div class="comments-header" id="<?php $this->respondId(); ?>" >
        <?php if($this->allow('comment')): ?>

          <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form">
                        <div class="cancel-comment-reply clear">
                            <?php $comments->cancelReply(); ?>
                        </div>
                          <?php if($this->user->hasLogin()): ?>
                              <h2 id="response" class="widget-title text-left"><?php _e('???????????????'); ?></h2>
                          <?php else: ?>
                            <h2 id="response" class="widget-title text-left"><?php _e('???????????????'); ?></h2>
                              <input type="text" name="author" id="author" placeholder="??????" value="<?php $this->remember('author'); ?>" />
                              <input type="email" name="mail" id="mail" placeholder="????????????" value="<?php $this->remember('mail'); ?>" />
                              <input type="text" name="url" id="url" placeholder="??????"  value="<?php $this->remember('url'); ?>" />
                          <?php endif; ?>
                          <p>
                              <input name="_" type="hidden" id="comment_" value="<?php echo Helper::security()->getToken(str_replace(array('?_pjax=%23pjax-container', '&_pjax=%23pjax-container'), '', Typecho_Request::getInstance()->getRequestUrl()));?>"/>
                              <textarea rows="5" name="text" id="textarea" placeholder="???????????????????????????..." style="resize:none;"><?php $this->remember('text'); ?></textarea>
                          </p>
                          <div class="clear">
                            <div class="OwO-logo" onclick="OwO_show()">
                              <span>(OwO)</span>
                            </div>
                            <button type="submit" class="submit"><?php _e('??????'); ?></button>
                          </div>
                          <div id="OwO-container"><?php  $this->need('owo.php'); ?></div>
                      </form>
        <?php endif; ?>
    </div>

    <?php if ($comments->have()): ?>
        <?php $comments->listComments(); ?>
        <?php $comments->pageNav('<?????????', '?????????>'); ?>
    <?php endif; ?>

</div>
