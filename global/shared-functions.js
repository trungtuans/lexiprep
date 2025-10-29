(function () {
  "use strict";
  // Convert html to markdown or text
  function htmlToMarkdown(htmlString, cleanup = false) {
    // Create an instance of TurndownService with default options.
    const turndownService = new TurndownService();
    let markdown = turndownService.turndown(htmlString);

    if (cleanup) {
      // Remove markdown links: transform [text](url) to just text.
      markdown = markdown.replace(/\[([^\]]+)\]\([^)]+\)/g, "$1");

      // Remove markdown images: transform ![alt](src) to just alt.
      markdown = markdown.replace(/!\[([^\]]*)\]\([^)]+\)/g, "$1");

      // Remove strong and emphasis markers: **text** or *text* become text.
      markdown = markdown.replace(/(\*\*|__)(.*?)\1/g, "$2");
      markdown = markdown.replace(/(\*|_)(.*?)\1/g, "$2");

      // Remove inline code markers and code blocks.
      markdown = markdown.replace(/`([^`]+)`/g, "$1");
      markdown = markdown.replace(/```[\s\S]*?```/g, "");

      // Remove ATX-style headings (# Heading).
      markdown = markdown.replace(/^#+\s*(.*?)$/gm, "$1");

      // Remove blockquote markers at the beginning of lines.
      markdown = markdown.replace(/^>\s*/gm, "");

      // Remove setext-style heading underlines.
      markdown = markdown.replace(/^(=+|-+)\s*$/gm, "");

      // Remove list markers.
      markdown = markdown.replace(/^(\s*)[-*+]\s+/gm, "$1");
      markdown = markdown.replace(/^(\s*)\d+\.\s+/gm, "$1");

      // Remove horizontal rules.
      markdown = markdown.replace(/^(\*\*\*|---|\*\*\*\*|____)\s*$/gm, "");

      // Remove table formatting.
      markdown = markdown.replace(/^\|(.+)\|$/gm, "$1");
      markdown = markdown.replace(/^[|:\-\s]+$/gm, "");

      // Remove escape characters for special Markdown syntax
      markdown = markdown.replace(/\\([[\]()*.#_`~+\-=|<>])/g, "$1"); // Handles most Markdown special chars
      markdown = markdown.replace(/(\d+)\\\./g, "$1."); // Special case for numbers followed by periods

      // Handle HTML entities
      markdown = markdown.replace(/&amp;/g, "&");
      markdown = markdown.replace(/&lt;/g, "<");
      markdown = markdown.replace(/&gt;/g, ">");
      markdown = markdown.replace(/&quot;/g, '"');
      markdown = markdown.replace(/&#39;/g, "'");

      // Clean up extra spaces and consecutive blank lines:
      // Trim whitespace for each line.
      markdown = markdown
        .split("\n")
        .map((line) => line.trim())
        .join("\n");

      // Replace multiple blank lines with a single blank line.
      markdown = markdown.replace(/\n{2,}/g, "\n\n");

      // Final trim to remove leading/trailing whitespace.
      markdown = markdown.trim();
    }

    return markdown;
  }

  /**
   * Modify the next AI reply once.
   * @param {string|null} replyText - Optional. Replace the entire AI reply if provided.
   * @param {string|null} appendText - Optional. Append extra text to the reply.
   */
  function modifyAIReply(replyText, appendText) {
    // Make sure MwaiAPI is available and supports addFilter
    if (!window.MwaiAPI || typeof MwaiAPI.addFilter !== "function") return;

    // Track if the modification has already been used once
    let used = false;

    // Add a filter to modify the AI reply
    MwaiAPI.addFilter("ai.reply", function (reply, args) {
      // If already used once, skip modification for later replies
      if (used) return reply;
      used = true;

      // Start with the original reply
      let finalReply = reply;

      // If replyText is given, replace the entire reply
      if (replyText) finalReply = replyText;

      // If appendText is given, add it after the reply
      if (appendText) finalReply += " " + appendText;

      // Return the modified reply to display to the user
      return finalReply;
    });
  }

  /**
   * Controls a chatbot instance.
   * @param {string|null} chatbotId - Target chatbot ID; null selects the default bot.
   * @param {boolean} shouldOpen - Open the chatbot UI if true.
   * @param {string|null} messageText - Optional message to queue or send.
   * @param {boolean} sendImmediately - Send the message right away when true.
   * @param {boolean} shouldClear - Clear chat history before any other action when true.
   * @param {boolean} isSystem - Wrap the message in system markup if true.
   * @param {function|null} callback - Invoked with the chatbot reply when provided.
   *
   * Examples:
   * controlChatbot(null, true, "Hello!", true, false);
   * controlChatbot(null, true, "What is 2+2?", true, false, false, (reply) => console.log(reply));
   * controlChatbot("my-bot", true, "System notification", true, false, true, (reply) => handleSystemReply(reply));
   */
  function controlChatbot(
    chatbotId,
    shouldOpen,
    messageText,
    sendImmediately,
    shouldClear,
    isSystem = false,
    callback = null
  ) {
    // Check if MwaiAPI is available
    if (!window.MwaiAPI || typeof MwaiAPI.getChatbot !== "function") {
      return false;
    }

    // Get the chatbot instance
    let chatbot = MwaiAPI.getChatbot(chatbotId);

    // Validate chatbot instance
    if (!chatbot) {
      return false;
    }

    // Set up response callback if provided
    if (callback && typeof callback === "function" && messageText) {
      // Helper function to process the chatbot's reply
      const handleReply = (reply) => {
        callback(reply); // Pass the reply to the callback
        console.log("Chatbot response processed successfully");
        return reply; // Return the reply unmodified
      };

      // Clear existing filters to ensure a fresh start for this interaction
      if (MwaiAPI.filters) {
        MwaiAPI.filters = {};
      }

      // Add a filter to intercept and handle the chatbot's reply
      MwaiAPI.addFilter("ai.reply", handleReply);
    }

    // Perform actions in logical order
    if (shouldClear) chatbot.clear(); // Clear first if requested
    if (shouldOpen) chatbot.open(); // Then open if needed
    if (messageText) {
      // Wrap system messages in special HTML tags
      const finalMessage = isSystem
        ? `<p class="lexi-system">${messageText}</p>`
        : messageText;

      // Add a small delay before sending to ensure filters are properly set
      setTimeout(() => {
        chatbot.ask(finalMessage, sendImmediately);
      }, 100);
    }

    return true;
  }

  /*
Name: highlightText
Description: Highlights specified text terms in the document with optional tooltips and smooth scrolling to the first match. Can be used to highlight single or multiple terms. Avoids highlighting terms that are under 4 characters and avoids highlighting over 1 paragraph.
Parameter:
- searchData (object): Object with term-tooltip pairs. Usage example: highlightText([["term1", "tooltip1"], ["term2", "tooltip2"]]). Use "" for no tooltip/highlight only.
- scopeSelector (string|null): Optional selector to limit the search area. Supports a single simple selector only: "#id", ".class", or "tag". For examaple highlightText("term", "#part-1") limits to element with id "part-1".
*/
  function highlightText(searchData, scopeSelector) {
    console.log("highlightText called with:", searchData, scopeSelector);
    const highlightClass = "lexi-text-highlight";
    const highlightedElements = [];
    const termTooltipMap = new Map();

    // Resolve scope roots based on a simple selector (#id, .class, or tag). Defaults to [document.body].
    function resolveScopeRoots(selector) {
      if (typeof document === "undefined" || !document.body) return [];
      if (!selector) return [document.body];
      const sel = String(selector).trim();
      if (!sel) return [document.body];
      // Only allow a single simple selector: no spaces, commas, or combinators/attributes
      if (/[,\s>+~\[]/.test(sel)) return [];
      if (sel.startsWith("#")) {
        const el = document.getElementById(sel.slice(1));
        return el ? [el] : [];
      }
      if (sel.startsWith(".")) {
        return Array.from(document.getElementsByClassName(sel.slice(1)));
      }
      // Treat as tag name
      if (!/^[a-zA-Z][\w-]*$/.test(sel)) return [];
      return Array.from(document.getElementsByTagName(sel));
    }

    // Normalize supported input formats into [{ term, tooltip }]
    function parseSearchData(data) {
      if (typeof data === "string") return [{ term: data, tooltip: undefined }];
      if (Array.isArray(data)) {
        return data.map((item) => {
          if (typeof item === "string")
            return { term: item, tooltip: undefined };
          if (Array.isArray(item)) return { term: item[0], tooltip: item[1] };
          if (item && typeof item === "object")
            return { term: item.term, tooltip: item.tooltip };
          return { term: String(item), tooltip: undefined };
        });
      }
      if (data && typeof data === "object") {
        return Object.entries(data).map(([term, tooltip]) => ({
          term,
          tooltip,
        }));
      }
      return [];
    }

    // Clear only highlights that match the current search terms
    function clearMatchingHighlights(roots, currentTerms) {
      const nodes = [];
      roots.forEach((root) => {
        nodes.push(...root.querySelectorAll(`.${highlightClass}`));
      });

      const currentTermsLower = currentTerms.map((t) => t.toLowerCase());

      nodes.forEach((el) => {
        const text = (el.textContent || "").toLowerCase();
        // Check if this highlight matches any of the current terms
        const shouldClear = currentTermsLower.some(
          (term) => text === term || text.includes(term) || term.includes(text)
        );

        if (shouldClear) {
          const parent = el.parentNode;
          if (parent) {
            parent.replaceChild(
              document.createTextNode(el.textContent || ""),
              el
            );
            parent.normalize();
          }
        }
      });
    }

    // Collect text nodes within scope roots (exclude scripts/styles and existing highlights)
    function getSearchableTextNodes(roots) {
      const nodes = [];
      roots.forEach((root) => {
        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
          acceptNode: (node) => {
            const parent = node.parentElement;
            if (!parent) return NodeFilter.FILTER_REJECT;
            const tag = parent.tagName && parent.tagName.toLowerCase();
            if (
              tag &&
              (tag === "script" || tag === "style" || tag === "noscript")
            )
              return NodeFilter.FILTER_REJECT;
            if (parent.classList && parent.classList.contains(highlightClass))
              return NodeFilter.FILTER_REJECT;
            if (!node.textContent || !node.textContent.trim())
              return NodeFilter.FILTER_REJECT;
            return NodeFilter.FILTER_ACCEPT;
          },
        });
        let n;
        while ((n = walker.nextNode())) nodes.push(n);
      });
      return nodes;
    }

    // Exact search for a term across provided nodes
    function findExactTextInNodes(searchText, textNodes) {
      if (!searchText) return [];
      const matches = [];
      const lowerSearch = searchText.toLowerCase();
      for (const node of textNodes) {
        const text = node.textContent || "";
        const lowerText = text.toLowerCase();
        let index = lowerText.indexOf(lowerSearch);
        while (index !== -1) {
          matches.push({
            node,
            start: index,
            end: index + searchText.length,
            text: searchText,
          });
          index = lowerText.indexOf(lowerSearch, index + 1);
        }
      }
      return matches;
    }

    // Fallback: progressively trim from end until any match is found
    function findTextWithFallbackInNodes(originalText, textNodes) {
      let searchText = (originalText || "").trim();
      let matches = [];
      while (searchText.length > 0) {
        matches = findExactTextInNodes(searchText, textNodes);
        if (matches.length > 0) break;
        searchText = searchText.slice(0, -1);
      }
      return matches;
    }

    function scrollToElement(el) {
      if (!el) return;
      el.scrollIntoView({
        behavior: "smooth",
        block: "center",
        inline: "nearest",
      });
    }

    if (typeof document === "undefined" || !document.body) return false;
    const scopeRoots = resolveScopeRoots(scopeSelector);
    if (!scopeRoots || scopeRoots.length === 0) return false;
    const termTooltipPairs = parseSearchData(searchData);

    // Extract current terms
    const currentTerms = termTooltipPairs.map(({ term }) => term);

    // Clear only highlights that match current search terms
    clearMatchingHighlights(scopeRoots, currentTerms);

    // Build tooltip map from input
    termTooltipPairs.forEach(({ term, tooltip }) => {
      if (tooltip !== undefined)
        termTooltipMap.set(String(term || "").toLowerCase(), tooltip);
    });

    const textNodes = getSearchableTextNodes(scopeRoots);
    if (textNodes.length === 0 || termTooltipPairs.length === 0) return false;

    // Gather matches for all terms
    const allMatchesByNode = new Map();
    for (const { term } of termTooltipPairs) {
      const matches = findTextWithFallbackInNodes(term, textNodes);
      for (const m of matches) {
        m.originalTerm = term;
        const arr = allMatchesByNode.get(m.node) || [];
        arr.push(m);
        allMatchesByNode.set(m.node, arr);
      }
    }

    // Apply highlights per node; avoid overlapping spans
    let firstCreated = null;
    for (const node of textNodes) {
      const nodeMatches = allMatchesByNode.get(node);
      if (!nodeMatches || nodeMatches.length === 0) continue;
      nodeMatches.sort(
        (a, b) => a.start - b.start || b.end - b.start - (a.end - a.start)
      );
      const nonOverlapping = [];
      let lastEnd = -1;
      for (const m of nodeMatches) {
        if (m.start >= lastEnd) {
          nonOverlapping.push(m);
          lastEnd = m.end;
        }
      }
      const original = node.textContent || "";
      const frag = document.createDocumentFragment();
      let cursor = 0;
      for (const m of nonOverlapping) {
        if (m.start > cursor) {
          frag.appendChild(
            document.createTextNode(original.substring(cursor, m.start))
          );
        }
        const span = document.createElement("span");
        span.className = highlightClass;
        span.textContent = original.substring(m.start, m.end);
        const tip = termTooltipMap.get(
          String(m.originalTerm || span.textContent).toLowerCase()
        );
        if (tip) span.setAttribute("data-tooltip-content", tip);
        frag.appendChild(span);
        highlightedElements.push(span);
        if (!firstCreated) firstCreated = span;
        cursor = m.end;
      }
      if (cursor < original.length) {
        frag.appendChild(document.createTextNode(original.substring(cursor)));
      }
      if (node.parentNode) node.parentNode.replaceChild(frag, node);
    }

    if (
      typeof window !== "undefined" &&
      window.lexi &&
      typeof window.lexi.toggleEffect === "function"
    ) {
      setTimeout(() => {
        window.lexi.toggleEffect(`.${highlightClass}`, "text-highlight", true, {
          direction: "left-to-right",
        });
      }, 300);
    }

    if (firstCreated) scrollToElement(firstCreated);
    return highlightedElements.length > 0;
  }

  /*
Example usage:
- highlightText("search term");
- highlightText(["term1", "term2"]);
- highlightText([["term1", "tooltip1"], ["term2", "tooltip2"]]);
- highlightText([{ term: "word", tooltip: "definition" }]);
- highlightText({ term1: "tooltip1", term2: "tooltip2" });
- highlightText("term", "#container"); // limit to element with id "container"
- highlightText("term", ".section");   // limit to elements with class "section"
- highlightText("term", "article");    // limit to all <article> elements
*/

  function clearTextHighlights() {
    const highlightClass = "lexi-text-highlight";
    const nodes = document.querySelectorAll(`.${highlightClass}`);
    nodes.forEach((el) => {
      const text = el.textContent || "";
      const parent = el.parentNode;
      if (parent) {
        parent.replaceChild(document.createTextNode(text), el);
        parent.normalize();
      }
    });
  }

  // Expose globally under a namespaced object
  if (typeof window !== "undefined") {
    window.lexi ??= {};
    window.lexi.htmlToMarkdown = htmlToMarkdown;
    window.lexi.modifyAIReply = modifyAIReply;
    window.lexi.controlChatbot = controlChatbot;
    window.lexi.highlightText = highlightText;
    window.lexi.clearTextHighlights = clearTextHighlights;
  }
})();
