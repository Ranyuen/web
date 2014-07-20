# coding=utf-8
require 'json'
require 'rexml/document'
require 'rexml/formatters/pretty'

class NavGenerator
  # param [String] dir
  # return [Hash]
  def gather_navs dir
    cwd = File.absolute_path '.'
    nav = {}
    Dir.chdir dir
    Dir.foreach '.' do |f|
      next if f == '.' || f == '..'
      if File.directory? f
        sub_nav = gather_navs File.absolute_path(f)
        sub_nav.each do |lang, n|
          nav[lang] ||= {}
          nav[lang][f] = n
        end
      elsif File.file?(f) && File.extname(f) == '.markdown'
        meta = get_meta f
        f = basename f
        next if f =~ /^error/
        lang = (f.match(/\.([^.]+)$/) || [])[1] || 'default'
        nav[lang] ||= {}
        nav[lang][basename f] = meta[:title]
      end
    end
    Dir.chdir cwd
    @nav = nav
    nav
  end

  # param [Hash] nav
  # return [String] sitemap XML string.
  def gen_sitemap nav = @nav
    sitemap = REXML::Document.new <<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
</urlset>
XML
    part = gen_sitemap_part nav['en'], 'http://ranyuen.com/en/'
    part.each{|url| sitemap.root.add_element url }
    part = gen_sitemap_part nav['ja'], 'http://ranyuen.com/'
    part.each{|url| sitemap.root.add_element url }
    formatter = REXML::Formatters::Pretty.new
    formatter.compact = true
    formatter.width = 120
    io = StringIO.new
    formatter.write sitemap, io
    io.string
  end

  private
  def get_meta filename
    content = open(filename, 'r:utf-8'){|f| f.read }
    return {} unless content =~ /^---/
    title =
      (content.lines.find{|line| line =~ /^title\s*:/ } || 'title:').
      match(/^title\s*:\s*(.*)$/)[1].chomp
    lastmod =
      (content.lines.find{|line| line =~ /^lastmod\s*:/ } || 'lastmod:').
      match(/^lastmod\s*:\s*(.*)$/)[1].chomp ||
      File.mtime(filename).strftime('%Y-%m-%dT%H:%M:%SZ')
    { title:   title,
      lastmod: lastmod }
  end

  def basename filename
    File.basename filename, File.extname(filename)
  end

  def gen_sitemap_part nav, base_url
    nodes = []
    nav.each do |key, child_nav|
      if child_nav.respond_to? :each
        nodes += gen_sitemap_part(child_nav, "#{base_url}#{key}/")
        next
      end
      next if key =~ /^error/
      url_node = REXML::Element.new 'url'
      loc_node = REXML::Element.new 'loc'
      loc_node.text = "#{base_url}#{key == 'index' ? '' : key}"
      url_node.add_element loc_node
      lastmod_node = REXML::Element.new 'lastmod'
      lastmod_node.text = Time.now.strftime '%Y-%m-%dT%H:%M:%SZ'
      url_node.add_element lastmod_node
      changefreq_node = REXML::Element.new 'changefreq'
      changefreq_node.text = 'daily'
      url_node.add_element changefreq_node
      nodes << url_node
    end
    nodes
  end
end

desc 'Generate site navigation JSON.'
task :gen_nav do
  g = NavGenerator.new
  nav = g.gather_navs File.absolute_path('templates')
  nav = nav.to_json
  open('templates/nav.json', 'w:utf-8'){|f| f.write nav }
  puts 'Generate navigation at templates/nav.json'
end

desc 'Generate sitemap.xml'
task :gen_sitemap => :gen_nav do
  nav = JSON.parse open('templates/nav.json', 'r:utf-8'){|f| f.read }
  g = NavGenerator.new
  sitemap = g.gen_sitemap nav
  open('sitemap.xml', 'w:utf-8'){|f| f.write sitemap }
  puts 'Generate sitemap at sitemap.xml'
end
# vim:set fdm=marker:
