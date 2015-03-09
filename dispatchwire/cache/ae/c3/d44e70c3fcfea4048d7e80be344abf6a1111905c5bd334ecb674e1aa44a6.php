<?php

/* _layouts/base.html */
class __TwigTemplate_aec3d44e70c3fcfea4048d7e80be344abf6a1111905c5bd334ecb674e1aa44a6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <style type=\"text/css\">
        * {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }
        ul {
            list-style: none;
            max-width: 800px;
            padding: 5px;
        }
        li {
            font-size: 14px;
            line-height: 2em;
            margin-bottom: 1px;
            padding: 5px 20px 5px 5px;
        }
            li.l1.odd,
            li.l2.even {
                background: #efefef;
            }
            li.l2 {
                padding-left: 40px;
            }
            li.dir {
                background: #fcf6df;
            }

            a.build-link {
                color: #0074d9;
                display: inline-block;
                float: right;
                font-size: .9em;
                padding: 0 15px;
                text-decoration: none;
            }
            a.build-pending {
                //color: #9a0f02;
            }
            a.build-successful {
                color: #038911;
            }
    </style>
</head>
<body>
";
        // line 52
        $this->displayBlock('content', $context, $blocks);
        // line 55
        echo "</body>
</html>";
    }

    // line 52
    public function block_content($context, array $blocks = array())
    {
        // line 53
        echo "
";
    }

    public function getTemplateName()
    {
        return "_layouts/base.html";
    }

    public function getDebugInfo()
    {
        return array (  83 => 53,  80 => 52,  75 => 55,  73 => 52,  20 => 1,);
    }
}
