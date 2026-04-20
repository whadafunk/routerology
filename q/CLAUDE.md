# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Purpose

The `/q` directory is a curated collection of technical interview questions and study materials covering networking, system administration, security, and infrastructure topics. It serves as both a personal knowledge base for learning and a reference for conducting technical interviews.

### Content Philosophy

- Questions are organized by topic and difficulty (fundamentals → advanced → practical → soft skills)
- Content focuses on foundational understanding rather than memorization
- Materials capture the "why" behind technologies, not just the "what"
- Questions are designed to test depth of knowledge and problem-solving ability

## Directory Structure

```
q/
├── interview-generic.md      # Main interview question bank (5 sections)
├── bash.md                   # Bash-specific technical questions
├── Readme.md                 # Directory overview and purpose
├── CLAUDE.md                 # This file
└── cover_photo.jpg          # Header image for documentation
```

## Organizing Interview Materials

### interview-generic.md Structure

The main interview question file is organized into 5 sections to support different interview contexts:

1. **Fundamentals & Core Concepts** (Questions 1-34)
   - Background, experience, and core theory
   - Operating systems, networking basics, core protocols
   - Use for: screening rounds, assessing fundamentals

2. **Troubleshooting & Practical Scenarios** (Exercises 1-6)
   - Real-world network configuration problems
   - Hands-on problem-solving scenarios
   - Use for: mid-level interviews, practical skills assessment

3. **Advanced Networking & Security** (Questions 35-67)
   - Firewalls, VPN, encryption, storage systems
   - Linux systems, Docker, advanced protocols
   - Use for: senior-level interviews, specialized skills

4. **Security, Threat Analysis & Defense** (Questions 68-71)
   - Threat modeling, attack analysis, defense strategies
   - Use for: security-focused roles

5. **Personal & Professional Development** (Questions 72-84)
   - Career growth, work habits, achievements, motivation
   - Use for: behavioral assessment, culture fit

### bash.md Structure

Dedicated section for Bash-specific technical topics:
- Shell fundamentals and advanced features
- SSH usage and tunneling
- Bash scripting patterns
- Use for: DevOps, SRE, infrastructure engineer interviews

## Guidelines for Maintaining Content

### Adding New Questions

- **Categorize clearly**: Decide which section (fundamentals, advanced, practical, security, or personal) the question belongs to
- **Maintain numbering**: Resequence all questions when adding new ones so they flow within sections
- **Check for duplicates**: Review existing questions to avoid redundancy before adding
- **Keep clarity**: Write questions that are specific and testable, not vague
- **Include context**: If a question has examples (e.g., IP addresses, protocols), make them concrete

### Refining Existing Questions

- **Flesh out vague topics**: If a question is just a topic name (e.g., "DHCP"), convert it to an actual question
- **Add use-case clarity**: Specify when or why a topic is important
- **Translate as needed**: Keep content in English for accessibility; translate foreign language questions when encountered
- **Remove duplicates**: Mark and consolidate redundant questions (identified by topic, not just exact wording)

### Documentation Standards

- Use consistent formatting: question numbers, topic headers, sub-points
- Link related questions where cross-topic understanding is helpful
- Provide examples where clarity requires them (network addresses, command formats, etc.)
- Avoid adding large solutions or full answers—questions should prompt thought, not provide answers

## Related Materials in Parent Repository

The parent `routerology/` repository contains hands-on materials and cheatsheets that complement these interview questions:

- **docker/** - Docker containerization concepts and practices
- **linux_networking/** - Linux networking configuration and troubleshooting
- **dhcp/** - DHCP protocol and server configuration
- **http-server-test/** - HTTP server testing and debugging
- **cheatsheets/** - Quick reference guides on various topics
- **meshcentral/** - Remote access and management systems
- **ldap-auth/** - LDAP authentication and directory services

When discussing interview questions, reference these materials for depth and hands-on context.

## Interview Preparation Tips

### For Interviewer (Using These Questions)

1. **Segment by level**: Use Section 1 (Fundamentals) for junior candidates, Sections 2-3 for mid-level, Section 4 for security specialists
2. **Mix question types**: Combine theoretical (Section 1), practical (Section 2), and advanced (Section 3) for balanced assessment
3. **Listen for depth**: Candidates should explain not just what something is, but why it matters
4. **Practical exercises**: Use Section 2 scenarios to assess real-world problem-solving—let candidates sketch solutions
5. **Behavioral balance**: Reserve Section 5 questions for the latter part of interviews after technical assessment

### Question Difficulty Progression

- Fundamentals section: Tests baseline knowledge
- Troubleshooting scenarios: Tests application of knowledge under constraints
- Advanced topics: Tests specialization and depth
- Soft skills: Tests fit and growth mindset

## Common Development/Maintenance Tasks

There are no build, test, or lint commands for this documentation repository. Maintenance is manual:

- **Content review**: Periodically review questions for clarity and relevance
- **Section rebalancing**: If one section grows too large, consider splitting or reorganizing
- **Topical cross-reference**: When updating a question, check if related questions in other sections need clarification
- **External link validation**: If adding references to the parent repo or external resources, verify links remain valid

## Notes for Future Maintainers

- This is a **living document**: Interview questions evolve as technologies change
- **Version control**: Use git commits with meaningful messages when restructuring (e.g., "Reorganize networking questions by OSI layer")
- **Consistency matters**: When multiple Claude instances work on this, maintain the section structure and numbering schema
- **Context preservation**: If questions reference external tools (nslookup, SSH, Docker), keep reference versions in mind—tools evolve
- **Cultural awareness**: Some questions include Romanian context—preserve but clarify for international interviews
