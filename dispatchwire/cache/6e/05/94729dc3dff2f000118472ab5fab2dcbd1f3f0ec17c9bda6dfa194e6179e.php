<?php

/* email-template-list.html */
class __TwigTemplate_6e0594729dc3dff2f000118472ab5fab2dcbd1f3f0ec17c9bda6dfa194e6179e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        try {
            $this->parent = $this->env->loadTemplate("_layouts/base.html");
        } catch (Twig_Error_Loader $e) {
            $e->setTemplateFile($this->getTemplateName());
            $e->setTemplateLine(1);

            throw $e;
        }

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "_layouts/base.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = array())
    {
        // line 3
        echo "    <ul>
";
        // line 4
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["file_tree"]) ? $context["file_tree"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 5
            echo "        <li class=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "item_cycle", array()), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "item_type", array()), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "item_level", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "item_name", array()), "html", null, true);
            if (($this->getAttribute($context["item"], "item_type", array()) == "file")) {
                echo "<a href=\"#\" class=\"build-link\" data-template-name=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "item_template_name", array()), "html", null, true);
                echo "\">Build</a>";
            }
            echo "</li>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 7
        echo "    </ul>
    <script type=\"text/javascript\" src=\"/tmp-ui/jquery.js\"></script>
    <script type=\"text/javascript\">
        \$('a.build-link').on('click', function(e) {
            e.preventDefault;

            \$(this).addClass('build-pending');

            \$.ajax({
                url: \"/build/\" + \$(this).data('template-name'),
                context: \$(this)
            }).done(function() {
                \$(this).removeClass('build-pending');
                \$(this).addClass('build-successful');
            });
        })
    </script>
";
    }

    public function getTemplateName()
    {
        return "email-template-list.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 7,  46 => 5,  42 => 4,  39 => 3,  36 => 2,  11 => 1,);
    }
}
